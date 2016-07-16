<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\PipsBackfill;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\PipsChange;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Walker\ForceIndexWalker;
use Doctrine\ORM\Query;

class PipsBackfillRepository extends PipsChangeRepository
{
    /**
     * @var PipsChange[]
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
     */
    public function findAndLockOldestUnprocessedItems(int $limit = 10)
    {
        $em = $this->getEntityManager();
        try {
            $em->getConnection()->beginTransaction();
            $query = $this->createQueryBuilder('pipsBackfill')
                ->where('pipsBackfill.processedTime IS NULL')
                ->andWhere('pipsBackfill.locked = 0')
                ->setMaxResults($limit)
                ->addOrderBy('pipsBackfill.cid', 'Asc')
                ->getQuery();

            $query->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
            // Extremely nasty hack to force doctrine to include FORCE INDEX in query
            $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, '\BBC\ProgrammesPagesService\Data\ProgrammesDb\Walker\ForceIndexWalker');
            $query->setHint(ForceIndexWalker::HINT_USE_INDEX, 'pips_backfill_locking_idx');
            /** @var PipsBackfill[] $result */
            $result = $query->getResult();
            $now = new \DateTime();
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

    public function setAsProcessed(PipsBackfill $change)
    {
        $change->setProcessedTime(new \DateTime());
        $change->setLockedAt(null);
        $change->setLocked(false);
        if (isset($this->lockedChanges[$change->getCid()])) {
            unset($this->lockedChanges[$change->getCid()]);
        }
        $this->addChange($change);
    }

    public function unlock(PipsBackfill $change)
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

    public function unlockAll()
    {
        $em = $this->getEntityManager();
        $cids = [];
        foreach ($this->lockedChanges as $change) {
            $cids[] = $change->getCid();
        }
        try {
            $em->getConnection()->beginTransaction();
            $query = $this->createQueryBuilder('pipsBackfill')
                ->where('pipsBackfill.cid IN (:cids)')
                ->andWhere('pipsBackfill.locked = 1')
                ->setParameter('cids', $cids)
                ->getQuery();
            $query->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
            /** @var PipsBackfill[] $results */
            $results = $query->getResult();
            foreach ($results as $changeEvent) {
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
        if ($em->isOpen()) {
            $this->unlockAll();
        }
    }
}
