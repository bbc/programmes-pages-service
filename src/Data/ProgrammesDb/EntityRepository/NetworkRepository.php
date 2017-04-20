<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class NetworkRepository extends EntityRepository
{
    public function findByUrlKeyWithDefaultService(string $urlKey): ?array
    {
        $qb = $this->createQueryBuilder('network')
            ->addSelect('defaultService')
            ->join('network.defaultService', 'defaultService')
            ->andWhere('network.urlKey = :urlKey')
            ->setParameter('urlKey', $urlKey);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
}
