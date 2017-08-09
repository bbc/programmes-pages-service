<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;

class PromotionRepository extends EntityRepository
{
    public function getPromotionsByPid(Pid $pid, ?int $limit, int $offset)
    {
        $qb = $this->createQueryBuilder('promotion')
            ->addSelect('context')
            ->addSelect('promotionOfCoreEntity')
            ->addSelect('promotionOfImage')
            ->lefJoin('promotion.context', 'context')
            ->lefJoin('promotion.promotionOfCoreEntity', 'promotionOfCoreEntity')
            ->lefJoin('promotion.promotionOfImage', 'promotionOfImage')
            ->andWhere('context.pid = :pid')
            ->addOrderBy('promotion.weighting', 'desc')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('pid', $pid);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function getSuperPromotionsByPid(Pid $pid, ?int $limit, int $offset)
    {

    }
}
