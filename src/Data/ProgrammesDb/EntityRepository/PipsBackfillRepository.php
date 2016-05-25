<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\PipsChange;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\PipsChangeBase;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PipsBackfillRepository extends PipsChangeRepository
{
    /** How long do locked rows stay locked for if they fail? */
    const LOCK_INTERVAL = 'PT5M';

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
            $expired = new \DateTime();
            $expired->sub(new \DateInterval(self::LOCK_INTERVAL));
            $em->getConnection()->beginTransaction();
            $query = $this->createQueryBuilder('pipsBackfill')
                ->where('pipsBackfill.processedTime IS NULL')
                ->andWhere('pipsBackfill.lockedAt IS NULL OR pipsBackfill.lockedAt < :expired')
                ->setMaxResults($limit)
                ->addOrderBy('pipsBackfill.cid', 'Asc')
                ->setParameter('expired', $expired, \Doctrine\DBAL\Types\Type::DATETIME)
                ->getQuery();

            $query->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
            $result = $query->getResult();
            $now = new \DateTime();
            foreach ($result as $item) {
                $item->setLockedAt($now);
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

    /**
     * This locks rows, so only call it when you're about to commit a transaction...
     *
     * @param mixed $cid
     * @return null|PipsChange
     */
    public function findById($cid)
    {
        $query = $this->createQueryBuilder('pipsBackfill')
            ->where('pipsBackfill.cid = :cid')
            ->setMaxResults(1)
            ->setParameter('cid', $cid)
            ->getQuery();

        $query->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
        $results = $query->getResult();
        if (!empty($results)) {
            return reset($results);
        }
        return null;
    }

    public function setAsProcessed(PipsChangeBase $change)
    {
        $change->setProcessedTime(new \DateTime());
        $change->setLockedAt(null);
        if (isset($this->lockedChanges[$change->getCid()])) {
            unset($this->lockedChanges[$change->getCid()]);
        }
        $this->addChange($change);
    }

    public function unlock(PipsChangeBase $change)
    {
        $change->setLockedAt(null);
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
        $expired = new \DateTime();
        $expired->sub(new \DateInterval(self::LOCK_INTERVAL));
        try {
            $em->getConnection()->beginTransaction();
            $query = $this->createQueryBuilder('pipsBackfill')
                ->where('pipsBackfill.cid IN (:cids)')
                ->andWhere('pipsBackfill.lockedAt IS NOT NULL OR pipsBackfill.lockedAt > :expired')
                ->setParameter('cids', $cids)
                ->setParameter('expired', $expired, \Doctrine\DBAL\Types\Type::DATETIME)
                ->getQuery();
            $query->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
            foreach ($query->getResult() as $changeEvent) {
                $changeEvent->setLockedAt(null);
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
