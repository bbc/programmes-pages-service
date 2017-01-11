<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class RelatedLinkRepository extends EntityRepository
{
    use Traits\SetLimitTrait;

    /**
     * @param array $dbIds
     * @param string $type
     * @param int|AbstractService::NO_LIMIT $limit
     * @param int $offset
     * @return mixed
     */
    public function findByRelatedTo(array $dbIds, string $type, ?int $limit, int $offset): array
    {
        $columnNameLookup = [
            'programme' => 'relatedToCoreEntity',
            'group' => 'relatedToCoreEntity',
            'promotion' => 'relatedToPromotion',
        ];
        $columnName = $columnNameLookup[$type] ?? 'relatedToCoreEntity';

        $qb = $this->createQueryBuilder('relatedLink')
            ->andWhere('relatedLink.' . $columnName . ' IN (:dbIds)')
            ->orderBy('relatedLink.position')
            ->addOrderBy('relatedLink.title')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('dbIds', $dbIds);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
