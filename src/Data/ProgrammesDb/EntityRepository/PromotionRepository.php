<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PromotionRepository extends EntityRepository
{
    use Traits\ParentTreeWalkerTrait;

    /**
     * @return array[]
     */
    public function findActivePromotionsByPid(Pid $pid, DateTimeImmutable $datetime, ?int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('promotion')
            ->addSelect(['promotionOfCoreEntity', 'promotionOfImage', 'imageForPromotionOfCoreEntity'])
            ->innerJoin('promotion.context', 'context')
            ->leftJoin('promotion.promotionOfCoreEntity', 'promotionOfCoreEntity')
            ->leftJoin('promotion.promotionOfImage', 'promotionOfImage')
            ->leftJoin('promotionOfCoreEntity.image', 'imageForPromotionOfCoreEntity')
            ->andWhere('promotion.isActive = 1')
            ->andWhere('promotion.startDate <= :datetime')
            ->andWhere('promotion.endDate > :datetime')
            ->andWhere('context.pid = :pid')
            ->addOrderBy('promotion.weighting', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('pid', $pid)
            ->setParameter('datetime', $datetime);

        $promotions = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return $this->resolveParentsForPromosOfCoreEntities($promotions);
    }

    /**
     * @param int[] $ancestryIds
     * @return array[]
     */
    public function findActiveSuperPromotionsByPid(array $ancestryIds, DateTimeImmutable $datetime, ?int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('promotion')
           ->addSelect(['promotionOfCoreEntity', 'promotionOfImage', 'imageForPromotionOfCoreEntity'])
           ->innerJoin('promotion.context', 'context')
           ->leftJoin('promotion.promotionOfCoreEntity', 'promotionOfCoreEntity')
           ->leftJoin('promotion.promotionOfImage', 'promotionOfImage')
           ->leftJoin('promotionOfCoreEntity.image', 'imageForPromotionOfCoreEntity')
           ->andWhere('promotion.isActive = 1')
           ->andWhere('promotion.startDate <= :datetime')
           ->andWhere('promotion.endDate > :datetime')
           ->andWhere('context.id in (:ancestryIds)')
           ->andWhere('promotion.cascadesToDescendants = 1')
           ->addOrderBy('promotion.weighting', 'ASC')
           ->setFirstResult($offset)
           ->setMaxResults($limit)
           ->setParameter('ancestryIds', $ancestryIds)
           ->setParameter('datetime', $datetime);

        $promotions = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return $this->resolveParentsForPromosOfCoreEntities($promotions);
    }

    /**
     * when the promotion is promoting a core_entity might happen that maybe this core entity has no an image to display,
     * so we have to go through the parents in order to find a valid image. So we have to resolve parents
     */
    private function resolveParentsForPromosOfCoreEntities(array $promotions): array
    {
        $coreEntitiesToResolve = [];
        foreach ($promotions as $i => $promotion) {
            if (!empty($promotion['promotionOfCoreEntity'])) {
                $coreEntitiesToResolve[$promotion['pid']] = $promotion;
            }
        }

        $resolvedPromotions = $this->resolveParents($coreEntitiesToResolve);

        foreach ($promotions as &$promotion) {
            if (isset($resolvedPromotions[$promotion['pid']])) {
                $promotion = $resolvedPromotions[$promotion['pid']];
            }
        }

        return $promotions;
    }

    /**
     * @param array[] $programmes
     */
    private function resolveParents(array $programmes): array
    {
        return $this->abstractResolveAncestry(
            $programmes,
            [$this, 'programmeAncestryGetter'],
            ['promotionOfCoreEntity', 'ancestry']
        );
    }

    /**
     * @param int[] $ids
     */
    private function programmeAncestryGetter(array $ids): array
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        return $repo->findByIds($ids);
    }
}
