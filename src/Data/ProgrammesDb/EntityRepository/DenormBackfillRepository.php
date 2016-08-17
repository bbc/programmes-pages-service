<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\DenormBackfill;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Walker\ForceIndexWalker;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;

class DenormBackfillRepository extends EntityRepository
{
    /**
     * @var DenormBackfill[]
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
     * @return \BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\DenormBackfill[]
     * @throws \Exception
     */
    public function findAndLockOldestUnprocessedItems(int $limit = 10)
    {
        $em = $this->getEntityManager();
        try {
            $em->getConnection()->beginTransaction();
            $query = $this->createQueryBuilder('denormBackfill')
                ->where('denormBackfill.processedTime IS NULL')
                ->andWhere('denormBackfill.locked = 0')
                ->setMaxResults($limit)
                ->addOrderBy('denormBackfill.id', 'Asc')
                ->getQuery();

            $query->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
            // Extremely nasty hack to force doctrine to include FORCE INDEX in query
            $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, '\BBC\ProgrammesPagesService\Data\ProgrammesDb\Walker\ForceIndexWalker');
            $query->setHint(ForceIndexWalker::HINT_USE_INDEX, 'denorm_backfill_locking_idx');
            /** @var DenormBackfill[] $result */
            $result = $query->getResult();
            $now = new \DateTime();
            foreach ($result as $item) {
                $item->setLockedAt($now);
                $item->setLocked(true);
                $em->persist($item);
                $this->lockedChanges[$item->getId()] = $item;
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

    public function setAsProcessed(DenormBackfill $change)
    {
        $change->setProcessedTime(new \DateTime());
        $change->setLockedAt(null);
        $change->setLocked(false);
        if (isset($this->lockedChanges[$change->getId()])) {
            unset($this->lockedChanges[$change->getId()]);
        }
        $this->addChange($change);
    }

    public function setMultipleAsProcessed(array $changes)
    {
        $em = $this->getEntityManager();
        foreach ($changes as $change) {
            $change->setProcessedTime(new \DateTime());
            $change->setLockedAt(null);
            $change->setLocked(false);
            if (isset($this->lockedChanges[$change->getId()])) {
                unset($this->lockedChanges[$change->getId()]);
            }
            $em->persist($change);
        }
        $em->flush();
    }

    public function unlock(DenormBackfill $change)
    {
        $change->setLockedAt(null);
        $change->setLocked(false);
        if (isset($this->lockedChanges[$change->getId()])) {
            unset($this->lockedChanges[$change->getId()]);
        }
        $em = $this->getEntityManager();
        if ($em->isOpen()) {
            $this->addChange($change);
        }
    }

    public function findByIds(array $ids)
    {
        $query = $this->createQueryBuilder('denormBackfill')
            ->where('denormBackfill.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery();
        return $query->getResult();
    }

    public function unlockAll()
    {
        $em = $this->getEntityManager();
        $ids = [];
        foreach ($this->lockedChanges as $change) {
            $ids[] = $change->getId();
        }
        try {
            $em->getConnection()->beginTransaction();
            $query = $this->createQueryBuilder('denormBackfill')
                ->where('denormBackfill.id IN (:ids)')
                ->andWhere('denormBackfill.locked = 1')
                ->setParameter('ids', $ids)
                ->getQuery();
            $query->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
            /** @var DenormBackfill[] $changes */
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
        if ($em->isOpen() && $em->getConnection()->ping()) {
            $this->unlockAll();
        }
    }

    public function addChange(DenormBackfill $change)
    {
        $em = $this->getEntityManager();
        $em->persist($change);
        $em->flush();
    }

    /**
     * @return DenormBackfill
     */
    public function findLatest()
    {
        try {
            return $this->findOneBy([], ['id' => 'Desc']);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @return DenormBackfill
     */
    public function findLatestProcessed()
    {
        try {
            $qb = $this->createQueryBuilder('denormBackfill')
                ->where('denormBackfill.processedTime IS NOT NULL')
                ->addOrderBy('denormBackfill.processedTime', 'Desc')
                ->setMaxResults(1);

            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function findLatestResults(int $limit = 10, $startId = null)
    {
        try {
            $qb = $this->createQueryBuilder('denormBackfill')
                ->where('denormBackfill.processedTime IS NULL')
                ->setMaxResults($limit)
                ->addOrderBy('denormBackfill.id', 'Desc');

            if ($startId) {
                $qb->andWhere('denormBackfill.id >= :id')
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
            $query = $this->createQueryBuilder('denormBackfill')
                ->where('denormBackfill.processedTime IS NULL')
                ->setMaxResults($limit)
                ->getQuery();

            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function getUnprocessedCount(): int
    {
        $query = $this->createQueryBuilder('denormBackfill')
            ->select('count(denormBackfill.id)')
            ->where('denormBackfill.processedTime IS NULL')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param mixed $id
     * @return null|DenormBackfill
     */
    public function findById($id)
    {
        return $this->find($id);
    }
}
