<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\PipsChange;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\PipsChangeBase;
use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class PipsChangeRepository extends EntityRepository
{
    public function addChange(PipsChangeBase $pipsChange)
    {
        $em = $this->getEntityManager();
        $em->persist($pipsChange);
        $em->flush();
    }

    public function addChanges(array $pipsChanges)
    {
        $em = $this->getEntityManager();
        foreach ($pipsChanges as $pipsChange) {
            if (!($pipsChange instanceof PipsChangeBase)) {
                throw new \InvalidArgumentException("pipsChange is not an instance of PipsChangeBase");
            }
            $em->persist($pipsChange);
        }
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
                ->addOrderBy('pipsChange.processedTime', 'Desc')
                ->setMaxResults(1);

            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @param int|AbstractService::NO_LIMIT $limit
     * @param null $startCid
     * @return array
     */
    public function findLatestResults($limit = 10, $startCid = null)
    {
        try {
            $qb = $this->createQueryBuilder('pipsChange')
                ->where('pipsChange.processedTime IS NULL')
                ->addOrderBy('pipsChange.cid', 'Desc')
                ->setMaxResults($limit);

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

    /**
     * @param int|AbstractService::NO_LIMIT $limit
     * @return array
     */
    public function findOldestUnprocessedItems($limit = 10)
    {
        try {
            $query = $this->createQueryBuilder('pipsChange')
                ->where('pipsChange.processedTime IS NULL')
                ->addOrderBy('pipsChange.cid', 'Asc')
                ->setMaxResults($limit)
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

    public function setAsProcessed(PipsChangeBase $change)
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

    public function deleteProcessedProcessedDateUntil(DateTimeImmutable $untilDate)
    {
        $query = $this->_em->createQueryBuilder()
                  ->delete($this->_entityName, 'pc')
                  ->where('pc.processedTime < :untildate')
                  ->andWhere('YEAR(pc.processedTime) <> 1970')
                  ->andWhere('pc.processedTime is not NULL')
                  ->setParameter(':untildate', $untilDate)
                  ->getQuery();

        $query->execute();
    }
}
