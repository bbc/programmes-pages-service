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

        $contextId = end($ancestryIds);
        $parentIds = array_slice($ancestryIds, 0, -1);

        $qb = $this->createQueryBuilder('promotion')
            ->addSelect(['promotionOfCoreEntity', 'promotionOfImage', 'imageForPromotionOfCoreEntity', 'IDENTITY(promotion.context) AS context_id'])
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

        if ($parentIds) {
            // If we are not in a top level core entity, fetch promotions and superpromotions.
            $qb->andWhere('promotion.context = :contextId OR (promotion.context IN (:parentIds) AND promotion.cascadesToDescendants = 1)')
               ->setParameter('contextId', $contextId)
               ->setParameter('parentIds', $parentIds);
        } else {
            // otherwise, there is no superpromotions to fetch, just fetch the promotion in the current context
            $qb->andWhere('promotion.context = :contextId')
               ->setParameter('contextId', $contextId);
        }

        $promotions = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        $promotions = $this->orderPromotionsByAncestryIds($promotions, $ancestryIds);

        // Because we are pulling in a scalar value (the context_id) in addition to the promotion entity Doctrine
        // returns each row as an array containing both the promotion and the scalar value. By this point we're done
        // with the context_id so we want to return just the promotion entities. Transforming each row from
        // [0 => [id => 1, /* The rest of a promotion's fields /*], 'context_id => 3] into
        // [id => 1, /* The rest of a promotion's fields /*]
        foreach ($promotions as &$promotion) {
            $promotion = $promotion[0];
        }

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

    /**
     * @param array[] $promotions
     * @param int[] $ancestryIds
     */
    private function orderPromotionsByAncestryIds(array $promotions, array $ancestryIds): array
    {
        $parentIds = array_slice($ancestryIds, 0, -1);
        // Order promos based upon where their context appears in the hierarchy. Promos of the current context should
        // be first, then promos belonging to the parent, then the promos belonging to the grandparent etc. Secondary
        // sort by the promo's weighting field if two promos have the same context.
        // If there are no parents, then the list is already sorted by weight from the DB query, so we can skip this
        // this sorting.
        if ($parentIds) {
            // We want ancestry IDs to be top so we need to build an ranking system to reflect that. Fortunatly that's
            // already in place - $ancestryIds is ordered and the location of that an ID in that field can be used to
            // determin order - the currentId is last so it has the highest rank. We can create this ranking lookup by
            // flipping the $ancestryIds array. Transforming [0 => grandparentId, 1 => parentId,  2 => currentId] into
            // [grandParentId => 0, parentId => 1, currentId => 2]
            $ancestryRank = array_flip($ancestryIds);

            usort($promotions, function(array $a, array $b) use ($ancestryRank) {
                $contextDifference = $ancestryRank[$b['context_id']] <=> $ancestryRank[$a['context_id']];

                // Sort based on context if they differ
                if ($contextDifference !== 0) {
                    return $contextDifference;
                }

                // If both promos have the same context then sort on weighting
                return $a[0]['weighting'] <=> $b[0]['weighting'];
            });
        }

        return $promotions;
    }
}
