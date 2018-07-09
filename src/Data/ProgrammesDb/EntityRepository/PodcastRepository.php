<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PodcastRepository extends EntityRepository
{
    public function findByCoreEntityId(int $coreEntityId, ?int $limit, int $offset): ?array
    {
        $qb = $this->createQueryBuilder('podcast')
            ->where('ce.id = :coreEntityId')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('coreEntityId', $coreEntityId);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function createQueryBuilder($alias, $indexBy = null)
    {
        // Any time podcasts are fetched here they must be inner joined to
        // their programme entity, this allows the embargoed filter to trigger
        // and exclude unwanted items.
        // This ensures that Podcasts that belong to an embargoed programme
        // are never returned
        return parent::createQueryBuilder($alias)
            ->addSelect('ce')
            ->join($alias . '.coreEntity', 'ce');
    }
}
