<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\PipsChange;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;

class PipsChangeRepository extends EntityRepository
{

    public function addChange(PipsChange $pipsChange)
    {
        $em = $this->getEntityManager();
        $em->persist($pipsChange);
        $em->flush();
    }

    /**
     * @return PipsChange
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
     * @return PipsChange
     */
    public function findLatestProcessed()
    {
        try {
            $qb = $this->createQueryBuilder('pipsChange')
                ->where('pipsChange.processedTime IS NOT NULL')
                ->addOrderBy('pipsChange.processedTime', 'Desc');

            return $qb->getQuery()->getOneOrNullResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function findLatestResults(int $limit = 10, $startCid = null)
    {
        try {
            $qb = $this->createQueryBuilder('pipsChange')
                ->where('pipsChange.processedTime IS NULL')
                ->setMaxResults($limit)
                ->addOrderBy('pipsChange.cid', 'Desc');

            if ($startCid) {
                $qb->andWhere('pipsChange.cid >= :cid')
                    ->setParameter(':cid', $startCid);
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
            $query = $this->createQueryBuilder('pipsChange')
                ->where('pipsChange.processedTime IS NULL')
                ->setMaxResults($limit)
                ->addOrderBy('pipsChange.cid', 'Asc')
                ->getQuery();

            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function getUnprocessedCount(): int
    {
        $query = $this->createQueryBuilder('pipsChange')
            ->select('count(pipsChange.cid)')
            ->where('pipsChange.processedTime IS NULL')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function setAsProcessed(PipsChange $change)
    {
        $change->setProcessedTime(new \DateTime());
        $this->addChange($change);
    }

    /**
     * @param mixed $cid
     * @return null|PipsChange
     */
    public function findById($cid)
    {
        return $this->find($cid);
    }
}
