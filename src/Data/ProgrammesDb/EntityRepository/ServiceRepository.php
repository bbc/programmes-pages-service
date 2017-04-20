<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ServiceRepository extends EntityRepository
{
    public function findBySids(array $serviceIds): array
    {
        $qb = $this->createQueryBuilder('service')
            ->addSelect(['network'])
            ->leftJoin('service.network', 'network')
            ->andWhere('service.sid IN (:dbIds)')
            ->setParameter('dbIds', $serviceIds);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function findByIds(array $ids): array
    {
        return $this->createQueryBuilder('service')
            ->addSelect(['masterBrand', 'network'])
            ->leftJoin('service.masterBrand', 'masterBrand')
            ->leftJoin('service.network', 'network')
            ->andWhere("service.id IN(:ids)")
            ->setParameter('ids', $ids)
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function findByPidFull(string $pid): ?array
    {
        $qb = $this->createQueryBuilder('service')
            ->addSelect('network')
            ->andWhere('service.pid = :pid')
            ->join('service.network', 'network')
            ->setParameter('pid', $pid);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findAllInNetworks(): array
    {
        $alias = 's';
        $qb = $this->createQueryBuilder($alias)
            ->select($alias, 'network')
            ->addSelect('CASE WHEN network.position IS NULL THEN 1 ELSE 0 AS HIDDEN hasPosition')
            ->join($alias . '.network', 'network')
            ->addOrderBy('hasPosition', 'ASC')
            ->addOrderBy('network.position', 'ASC')
            ->addOrderBy($alias . '.shortName', 'ASC');

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
