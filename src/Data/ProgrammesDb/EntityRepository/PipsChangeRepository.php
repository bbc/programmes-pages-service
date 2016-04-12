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
            return $this->findOneBy([
                'processedTime' => null,
            ], ['cid' => 'Desc']);
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

            return $query->getArrayResult();
        } catch (NoResultException $e) {
            return [];
        }
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
