<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

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
        // This ensures that SegmentEvents that belong to an embargoed programme
        // are never returned
        return parent::createQueryBuilder($alias)
            ->join($alias . '.programmeItem', 'p')
            ->andWhere('p.isEmbargoed = false');
    }
}
