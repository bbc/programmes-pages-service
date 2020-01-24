<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class MasterBrandRepository extends EntityRepository {
    public function findByMid(string $mid): ?array
    {
        $qb = $this->createQueryBuilder('master_brand')
            ->addSelect(['master_brand', 'network', 'service'])
            ->leftJoin('master_brand.network', 'network')
            ->leftJoin('network.defaultService', 'service')
            ->andWhere('master_brand.mid = :mid')
            ->setParameter('mid', $mid);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
}
