<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class RelatedLinkRepository extends EntityRepository
{
    /**
     * @param array $dbIds
     * @param string $relatedToEntityType The type of entity the link is related to.
     * @param string[] $linkTypes An array of links types to filter by. Passing an empty array will return all types.
     * @param int|AbstractService::NO_LIMIT $limit
     * @param int $offset
     * @return array
     */
    public function findByRelatedTo(array $dbIds, string $relatedToEntityType, array $linkTypes, ?int $limit, int $offset): array
    {
        $columnNameLookup = [
            'programme' => 'relatedToCoreEntity',
            'group' => 'relatedToCoreEntity',
            'promotion' => 'relatedToPromotion',
        ];
        $columnName = $columnNameLookup[$relatedToEntityType] ?? 'relatedToCoreEntity';

        $qb = $this->createQueryBuilder('relatedLink')
            ->andWhere('relatedLink.' . $columnName . ' IN (:dbIds)')
            ->orderBy('relatedLink.position')
            ->addOrderBy('relatedLink.title')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('dbIds', $dbIds);

        // Filter to a subset of types if requested
        if ($linkTypes) {
            $qb->andWhere('relatedLink.type IN (:types)')
                ->setParameter('types', $linkTypes);
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
