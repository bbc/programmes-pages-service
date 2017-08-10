<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Domain\ApplicationTime;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PromotionRepository extends EntityRepository
{
    public function findActivePromotionsByPid(Pid $pid, ?int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('promotion')
            ->addSelect('context')
            ->addSelect('promotionOfCoreEntity')
            ->addSelect('promotionOfImage')
            ->leftJoin('promotion.context', 'context')
            ->leftJoin('promotion.promotionOfCoreEntity', 'promotionOfCoreEntity')
            ->leftJoin('promotion.promotionOfImage', 'promotionOfImage')
            ->andWhere('promotion.isActive = 1')
            ->andWhere('promotion.startDate <= :now')
            ->andWhere('promotion.endDate >= :now')
            ->andWhere('context.pid = :pid')
            ->addOrderBy('promotion.weighting', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('pid', $pid)
            ->setParameter('now', ApplicationTime::getTime());

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * @param int[] $ancestryIds
     */
    public function findActiveSuperPromotionsByPid(array $ancestryIds, ?int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('promotion')
           ->addSelect('context')
           ->addSelect('promotionOfCoreEntity')
           ->addSelect('promotionOfImage')
           ->leftJoin('promotion.context', 'context')
           ->leftJoin('promotion.promotionOfCoreEntity', 'promotionOfCoreEntity')
           ->leftJoin('promotion.promotionOfImage', 'promotionOfImage')
           ->andWhere('promotion.isActive = 1')
           ->andWhere('promotion.startDate <= :now')
           ->andWhere('promotion.endDate >= :now')
           ->andWhere('context.id in (:ancestryIds)')
           ->andWhere('promotion.cascadesToDescendants = 1')
           ->addOrderBy('promotion.weighting', 'ASC')
           ->setFirstResult($offset)
           ->setMaxResults($limit)
           ->setParameter('ancestryIds', $ancestryIds)
           ->setParameter('now', ApplicationTime::getTime());

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
