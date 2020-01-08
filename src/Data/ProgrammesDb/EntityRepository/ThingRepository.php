<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ThingRepository extends EntityRepository
{
    public function findById(string $id): ?array
    {
        return $this->createQueryBuilder('thing')
            ->andWhere('thing.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
}
