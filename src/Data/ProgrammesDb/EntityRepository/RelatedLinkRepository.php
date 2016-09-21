<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class RelatedLinkRepository extends EntityRepository
{
    public function findByRelatedTo(array $dbIds, string $type, $limit, $offset)
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
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter('dbIds', $dbIds);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
