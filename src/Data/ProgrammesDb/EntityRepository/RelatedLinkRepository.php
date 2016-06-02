<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class RelatedLinkRepository extends EntityRepository
{
    public function findByRelatedTo(array $pids, string $type, int $limit, int $offset)
    {
        $columnNameLookup = [
            'programme' => 'relatedToCoreEntity',
            'group' => 'relatedToCoreEntity',
            'promotion' => 'relatedToPromotion',
        ];
        $columnName = $columnNameLookup[$type] ?? 'relatedToCoreEntity';

        $qb = $this->createQueryBuilder('relatedLink')
            ->innerJoin('relatedLink.' . $columnName, 'relatedTo')
            ->andWhere('relatedTo.pid IN (:pids)')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter('pids', $pids);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
