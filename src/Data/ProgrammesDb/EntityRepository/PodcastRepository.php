<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PodcastRepository extends EntityRepository
{
    public function findByCoreEntityId(int $coreEntityId, ?int $limit, int $offset): ?array
    {
        $qb = $this->createQueryBuilder('podcast')
            ->addSelect('coreEntity')
            ->join('podcast.coreEntity', 'coreEntity')
            ->where('coreEntity.id = :coreEntityId')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('coreEntityId', $coreEntityId);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
}
