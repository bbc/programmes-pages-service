<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ServiceRepository extends EntityRepository
{
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

    public function findAllInNetworkActiveOn(Nid $networkId, ?DateTimeImmutable $date = null): array
    {
        $qb = $this->createQueryBuilder('service')
            ->addSelect('network')
            ->join('service.network', 'network')
            ->andWhere('network.nid = :networkId');

        if ($date) {
            $qb->andWhere(
                '(service.startDate IS NULL OR service.startDate <= :date)'
            );
            $qb->andWhere(
                '(service.endDate IS NULL OR service.endDate > :date)'
            );
            $qb->setParameter('date', $date);
        }

        $qb->setParameter('networkId', $networkId)
            ->addOrderBy('service.shortName', 'ASC');

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
