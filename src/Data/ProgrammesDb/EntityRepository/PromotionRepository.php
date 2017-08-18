<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use InvalidArgumentException;

class PromotionRepository extends EntityRepository
{
    use Traits\ParentTreeWalkerTrait;

    /**
     * @param int[] $ancestryIds
     * @return array[]
     * @throws InvalidArgumentException when we pass an empty array of ancestryIds
     */
    public function findActivePromotionsByContext(array $ancestryIds, DateTimeImmutable $datetime, ?int $limit, int $offset): array
    {
        // $contextId is the current context. We want to get both cascading and non-cascading promos for this id
        // $ancestryIds is an array ids of the parents of the current context. For these ids we only want cascading promos
        if (empty($ancestryIds)) {
            throw new InvalidArgumentException('ancestryIds cannot be an empty value');
        }

        $contextId = array_pop($ancestryIds);

        $qb = $this->createQueryBuilder('promotion')
            ->addSelect(['promotionOfCoreEntity', 'promotionOfImage', 'imageForPromotionOfCoreEntity'])
            ->leftJoin('promotion.promotionOfCoreEntity', 'promotionOfCoreEntity')
            ->leftJoin('promotion.promotionOfImage', 'promotionOfImage')
            ->leftJoin('promotionOfCoreEntity.image', 'imageForPromotionOfCoreEntity')
            ->andWhere('promotion.isActive = 1')
            ->andWhere('promotion.startDate <= :datetime')
            ->andWhere('promotion.endDate > :datetime')
            ->addOrderBy('promotion.weighting', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('datetime', $datetime);

        if (!$ancestryIds) {
            $qb->andWhere('promotion.context = :contextId')
                ->setParameter('contextId', $contextId);
        } else {
            $qb->andWhere('promotion.context = :contextId OR (promotion.context IN (:ancestryIds) AND promotion.cascadesToDescendants = 1)')
                ->setParameter('contextId', $contextId)
                ->setParameter('ancestryIds', $ancestryIds);
        }

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
     * @param array[] $entities
     */
    private function resolveParents(array $entities): array
    {
        return $this->abstractResolveAncestry(
            $entities,
            [$this, 'coreEntityAncestryGetter'],
            ['promotionOfCoreEntity', 'ancestry']
        );
    }

    /**
     * @param int[] $ids
     */
    private function coreEntityAncestryGetter(array $ids): array
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        return $repo->findByIds($ids);
    }
}
