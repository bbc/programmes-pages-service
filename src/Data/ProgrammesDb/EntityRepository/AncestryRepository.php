<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class AncestryRepository extends EntityRepository
{
    // public function findByCoreEntityId(string $pid): ?array
    // {
    //     $qb = parent::createQueryBuilder('ancestor')
    //         ->andWhere('ancestor.pid = :pid')
    //         ->setParameter('pid', $pid);

    //     return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    // }

}
