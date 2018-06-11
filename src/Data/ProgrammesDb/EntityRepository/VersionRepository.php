<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class VersionRepository extends EntityRepository
{
    public const ALTERNATE_VERSION_TYPES = [
        'AudioDescribed',
        'DubbedAudioDescribed',
        'OpenSubtitled',
        'Signed',
    ];

    public function findByPid(string $pid): ?array
    {
        $qb = $this->createQueryBuilder('version')
            ->andWhere('version.pid = :pid')
            ->setParameter('pid', $pid);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findByPidFull(string $pid): ?array
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
            ->addSelect(['versionTypes'])
            ->leftJoin('version.versionTypes', 'versionTypes')
            ->andWhere('version.pid = :pid')
            ->setParameter('pid', $pid);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findByProgrammeItem(string $programmeDbId): array
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
            ->addSelect(['versionTypes'])
            ->leftJoin('version.versionTypes', 'versionTypes')
            ->andWhere('version.programmeItem = :dbId')
            ->addOrderBy('version.pid', 'ASC')
            ->setParameter('dbId', $programmeDbId);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function findOriginalVersionForProgrammeItem(string $programmeDbId): ?array
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
            ->addSelect(['versionTypes'])
            ->leftJoin('version.versionTypes', 'versionTypes')
            ->andWhere("versionTypes.type = 'Original'")
            ->andWhere('version.programmeItem = :dbId')
            ->setParameter('dbId', $programmeDbId);

        // In some cases, an episode can have more than one Original version.
        // We account for that by returning only the first Original version we find.
        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY)[0] ?? null;
    }

    /**
     * Returns the programme item's canonical streamable version (p.streamableVersion) FIRST.
     * @param string $programmeDbId
     * @return array
     */
    public function findStreamableByProgrammeItem(string $programmeDbId): array
    {
        $qb = $this->createQueryBuilder('version')
            ->addSelect([
                'versionTypes',
                'CASE WHEN (IDENTITY(p.streamableVersion) = version.id) THEN 1 ELSE 0 AS HIDDEN isStreamable',
            ])
            ->leftJoin('version.versionTypes', 'versionTypes')
            ->where('p.id = :dbId')
            ->andWhere('version.streamable = 1')
            ->andWhere('versionTypes.type NOT IN (:alternateVersionTypes)')
            ->orderBy('isStreamable', 'DESC')
            ->addOrderBy('version.pid', 'ASC')
            ->setParameter('dbId', $programmeDbId)
            ->setParameter('alternateVersionTypes', self::ALTERNATE_VERSION_TYPES);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function findAvailableByProgrammeItem(string $programmeDbId): array
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
            ->addSelect(['versionTypes'])
            ->leftJoin('version.versionTypes', 'versionTypes')
            ->andWhere('version.programmeItem = :dbId')
            ->andWhere('version.streamable = 1 OR version.downloadable = 1')
            ->addOrderBy('version.pid', 'ASC')
            ->setParameter('dbId', $programmeDbId);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function createQueryBuilder($alias, $indexBy = null)
    {
        // Any time versions are fetched here they must be inner joined to
        // their programme entity, this allows the embargoed filter to trigger
        // and exclude unwanted items.
        // This ensures that Versions that belong to an embargoed programme
        // are never returned
        return parent::createQueryBuilder($alias)
            ->addSelect('p')
            ->join($alias . '.programmeItem', 'p');
    }
}
