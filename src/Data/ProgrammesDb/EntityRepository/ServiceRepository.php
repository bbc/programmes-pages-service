<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ServiceRepository extends EntityRepository
{
    public function findByIds(array $serviceIds)
    {
        $qb = $this->createQueryBuilder('service')
            ->addSelect(['network'])
            ->leftJoin('service.network', 'network')
            ->where('service.sid IN (:dbIds)')
            ->setParameter('dbIds', $serviceIds);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
