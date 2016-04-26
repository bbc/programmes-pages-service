<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class VersionRepository extends EntityRepository
{
    public function findByPid(string $pid)
    {
        $qb = $this->getQueryBuilder()
            ->andWhere('version.pid = :pid')
            ->setParameter('pid', $pid);
        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    private function getQueryBuilder(): QueryBuilder
    {
        // Any time versions are fetched here they must be joined to their
        // programme entity and checked for embargo.
        return $this->createQueryBuilder('version')
            ->join('version.programmeItem', 'p')
            ->andWhere('p.isEmbargoed = false');
    }
}
