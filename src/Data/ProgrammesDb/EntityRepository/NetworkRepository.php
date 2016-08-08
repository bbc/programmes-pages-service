<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class NetworkRepository extends EntityRepository
{
    public function findByUrlKeyWithDefaultService($urlKey)
    {
        $qb = $this->createQueryBuilder('network')
            ->addSelect('defaultService')
            ->join('network.defaultService', 'defaultService')
            ->where('network.urlKey = :urlKey')
            ->setParameter('urlKey', $urlKey);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
}
