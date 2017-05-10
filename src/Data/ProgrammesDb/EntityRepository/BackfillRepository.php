<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\BackfillBase;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Walker\ForceIndexWalker;
use DateTime;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;

class BackfillRepository extends EntityRepository
{
    /**
     * @var BackfillBase[]
     */
    private $lockedChanges = array();

    /**
     * This uses SELECT FOR UPDATE under the bonnet
     * it will block any thread attempting to select these rows
     * until the lock is released. Which is roughly what we want here,
     * but obviously we need to be quick about it.
     * it MUST be put within a transaction of its own
     *
     * Also note that START TRANSACTION implicitly commits any transaction
     * that has already started, so NEVER call this with an open transaction.
     * MySQL does not support nested transactions.
     *
     * @param int $limit
     * @return \BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\BackfillBase[]
     * @throws \Exception
     */
    public function findAndLockOldestUnprocessedItems(int $limit = 10)
    {
        $em = $this->getEntityManager();
        try {
            $em->getConnection()->beginTransaction();
            $query = $this->createQueryBuilder('backfill')
                ->andWhere('backfill.processedTime IS NULL')
                ->andWhere('backfill.locked = 0')
                ->setMaxResults($limit)
                ->addOrderBy('backfill.cid', 'Asc')
                ->getQuery();

            $query->setLockMode(LockMode::PESSIMISTIC_WRITE);
            // Extremely nasty hack to force doctrine to include FORCE INDEX in query
            $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, '\BBC\ProgrammesPagesService\Data\ProgrammesDb\Walker\ForceIndexWalker');
            $query->setHint(ForceIndexWalker::HINT_USE_INDEX, $this->getCompoundIndexName());
            /** @var BackfillBase[] $result */
            $result = $query->getResult();
            $now = new DateTime();
            foreach ($result as $item) {
                $item->setLockedAt($now);
                $item->setLocked(true);
                $em->persist($item);
                $this->lockedChanges[$item->getCid()] = $item;
            }
            $em->flush();
            $em->getConnection()->commit();
            return $result;
        } catch (\Exception $e) {
            // catch everything, in order to rollback the transaction before re-throw
            $em->getConnection()->rollBack();
            $this->lockedChanges = [];
            throw $e;
        }
    }

    public function setAsProcessed(BackfillBase $change)
    {
        $change->setProcessedTime(new DateTime());
        $change->setLockedAt(null);
        $change->setLocked(false);
        if (isset($this->lockedChanges[$change->getCid()])) {
            unset($this->lockedChanges[$change->getCid()]);
        }
        $this->addChange($change);
    }

    public function setMultipleAsProcessed(array $changes)
    {
        $em = $this->getEntityManager();
        foreach ($changes as $change) {
            $change->setProcessedTime(new DateTime());
            $change->setLockedAt(null);
            $change->setLocked(false);
            if (isset($this->lockedChanges[$change->getCid()])) {
                unset($this->lockedChanges[$change->getCid()]);
            }
            $em->persist($change);
        }
        $em->flush();
    }

    public function unlock(BackfillBase $change)
    {
        $change->setLockedAt(null);
        $change->setLocked(false);
        if (isset($this->lockedChanges[$change->getCid()])) {
            unset($this->lockedChanges[$change->getCid()]);
        }
        $em = $this->getEntityManager();
        if ($em->isOpen()) {
            $this->addChange($change);
        }
    }

    public function findByIds(array $ids)
    {
        $query = $this->createQueryBuilder('backfill')
            ->andWhere('backfill.cid IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery();
        return $query->getResult();
    }

    public function unlockAll()
    {
        $em = $this->getEntityManager();
        $ids = [];
        foreach ($this->lockedChanges as $change) {
            $ids[] = $change->getCid();
        }
        try {
            $em->getConnection()->beginTransaction();
            $query = $this->createQueryBuilder('backfill')
                ->andWhere('backfill.cid IN (:ids)')
                ->andWhere('backfill.locked = 1')
                ->setParameter('ids', $ids)
                ->getQuery();
            $query->setLockMode(LockMode::PESSIMISTIC_WRITE);
            /** @var BackfillBase[] $changes */
            $changes = $query->getResult();
            foreach ($changes as $changeEvent) {
                $changeEvent->setLockedAt(null);
                $changeEvent->setLocked(false);
                $em->persist($changeEvent);
            }
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            // catch everything, in order to rollback the transaction before re-throw
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * This is not stupid.
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function __destruct()
    {
        // Unlock all rows on shutdown (if we can)
        $em = $this->getEntityManager();
        try {
            if ($em->isOpen() && $em->getConnection()->ping()) {
                $this->unlockAll();
            }
        } catch (\Exception $e) {
            return;
        }
    }

    public function addChange(BackfillBase $change)
    {
        $em = $this->getEntityManager();
        $em->persist($change);
        $em->flush();
    }

    /**
     * @param BackfillBase[] $changes
     */
    public function addChanges(array $changes)
    {
        $em = $this->getEntityManager();
        foreach ($changes as $change) {
            $em->persist($change);
        }
        $em->flush();
    }

    /**
     * @return BackfillBase
     */
    public function findLatest()
    {
        try {
            return $this->findOneBy([], ['cid' => 'Desc']);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @return BackfillBase
     */
    public function findLatestProcessed()
    {
        try {
            $qb = $this->createQueryBuilder('backfill')
                ->andWhere('backfill.processedTime IS NOT NULL')
                ->addOrderBy('backfill.processedTime', 'Desc')
                ->setMaxResults(1);

            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function findLatestResults(int $limit = 10, $startId = null)
    {
        try {
            $qb = $this->createQueryBuilder('backfill')
                ->andWhere('backfill.processedTime IS NULL')
                ->setMaxResults($limit)
                ->addOrderBy('backfill.cid', 'Desc');

            if ($startId) {
                $qb->andWhere('backfill.cid >= :id')
                    ->setParameter(':id', $startId);
            }
            $query = $qb->getQuery();

            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findOldestUnprocessedItems(int $limit = 10)
    {
        try {
            $query = $this->createQueryBuilder('backfill')
                ->andWhere('backfill.processedTime IS NULL')
                ->setMaxResults($limit)
                ->getQuery();

            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function getUnprocessedCount(): int
    {
        $query = $this->createQueryBuilder('backfill')
            ->select('count(backfill.cid)')
            ->andWhere('backfill.processedTime IS NULL')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param mixed $id
     * @return null|BackfillBase
     */
    public function findById($id)
    {
        return $this->find($id);
    }

    protected function getCompoundIndexName(): string
    {
        $classMetaData = $this->_class;
        $indexes = $classMetaData->table['indexes'];
        foreach ($indexes as $indexName => $index) {
            if (strpos($indexName, 'locking_idx') !== false) {
                return $indexName;
            }
        }
        throw new \RuntimeException('Cannot find locking index for table ' . $this->_class->getTableName());
    }
}
