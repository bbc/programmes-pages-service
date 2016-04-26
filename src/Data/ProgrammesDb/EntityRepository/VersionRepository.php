<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class VersionRepository extends EntityRepository
{
    public function findByPid(string $pid)
    {
        $qb = $this->createQueryBuilder('version')
            ->andWhere('version.pid = :pid')
            ->setParameter('pid', $pid);
        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function createQueryBuilder($alias, $indexBy = null)
    {
        // Any time versions are fetched here they must be joined to their
        // programme entity and checked for embargo.
        return parent::createQueryBuilder($alias)
            ->join('version.programmeItem', 'p')
            ->andWhere('p.isEmbargoed = false');
    }
}
