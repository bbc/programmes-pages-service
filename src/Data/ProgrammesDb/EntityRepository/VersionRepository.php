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

    public function findByProgrammeItem(string $programmeDbId)
    {
        // YIKES! versionTypes is a many-to-many join, that could result in
        // an increase of rows returned by the DB and the potential for slow DB
        // queries as per https://ocramius.github.io/blog/doctrine-orm-optimization-hydration/.
        // Except it doesn't - the vast majority of Versions only have one
        // versionType. At time of writing this comment (June 2016) only 0.5% of
        // the Versions in PIPS have 2 or more VersionTypes and the most
        // VersionTypes a version has is 4. Creating an few extra rows in very
        // rare cases is way more efficient that having to do a two-step
        // hydration process.

        $qb = $this->createQueryBuilder('version')
            ->addSelect(['p', 'versionTypes'])
            ->leftJoin('version.versionTypes', 'versionTypes')
            ->andWhere('version.programmeItem = :dbId')
            ->addOrderBy('version.pid', 'ASC')
            ->setParameter('dbId', $programmeDbId);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
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
