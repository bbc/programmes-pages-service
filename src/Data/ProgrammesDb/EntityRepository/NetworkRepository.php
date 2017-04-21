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

    public function findPublishedNetworksByType(array $types, ?int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('network')
            ->addSelect('defaultService')
            ->join('network.defaultService', 'defaultService')
            ->andWhere('network.position IS NOT NULL')
            ->andWhere('network.type IN (:types)')
            ->addOrderBy('network.position', 'ASC')
            ->addOrderBy('network.name', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('types', $types);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
