<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class VersionRepository extends EntityRepository
{
    use Traits\ParentTreeWalkerTrait;
    /**
     * This is the list of versions that iPlayer does not playout at
     * https://www.bbc.co.uk/iplayer/episode/{pid} but instead either
     * https://www.bbc.co.uk/iplayer/episode/{pid}/sign or
     * https://www.bbc.co.uk/iplayer/episode/{pid}/ad
     *
     * @var string[]
     */
    public const ALTERNATE_VERSION_TYPES = [
        'DubbedAudioDescribed',
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
    public function findAllStreamableByProgrammeItem(string $programmeDbId): array
    {
        $qb = $this->createQueryBuilder('version')
            ->addSelect([
                'versionTypes',
                'CASE WHEN (IDENTITY(p.streamableVersion) = version.id) THEN 1 ELSE 0 END AS HIDDEN isStreamable',
                'masterBrand',
                'competitionWarning',
                'competitionWarningProgrammeItem',
            ])
            ->innerJoin('version.versionTypes', 'versionTypes')
            // This second join is a hack. We need to retrieve all the version types, but filter out
            // any versions with only alternate types
            ->innerJoin('version.versionTypes', 'versionTypesSelect')
            ->leftJoin('p.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.competitionWarning', 'competitionWarning')
            ->leftJoin('competitionWarning.programmeItem', 'competitionWarningProgrammeItem')
            ->where('p.id = :dbId')
            ->andWhere('version.streamable = 1')
            ->andWhere('versionTypesSelect.type NOT IN (:alternateVersionTypes)')
            ->orderBy('isStreamable', 'DESC')
            ->addOrderBy('version.pid', 'ASC')
            ->setParameter('dbId', $programmeDbId)
            ->setParameter('alternateVersionTypes', self::ALTERNATE_VERSION_TYPES);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return $this->hydrateCompetitionWarnings($result);
    }

    public function findAlternateVersionsForProgrammeItem(string $programmeDbId): array
    {
        $qb = $this->createQueryBuilder('version')
            ->addSelect([
                'versionTypes',
            ])
            ->innerJoin('version.versionTypes', 'versionTypes')
            ->where('p.id = :dbId')
            ->andWhere('version.streamable = 1')
            ->andWhere('versionTypes.type IN (:alternateVersionTypes)')
            ->setParameter('dbId', $programmeDbId)
            ->setParameter('alternateVersionTypes', self::ALTERNATE_VERSION_TYPES);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * This method gets all of the special versions Faucet links against a ProgrammeItem. That is,
     * the streamableVersion (the version that should be played out/linked to in playout), the
     * canonicalVersion (the version that should be used to display segment events), and the
     * downloadableVersion (the version that should be linked to for downloads/podcasts)
     *
     * @param string $programmeItemDbId
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLinkedVersionsForProgrammeItem(string $programmeItemDbId): ?array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select([
                'p', 'streamableVersion', 'streamableVersionTypes', 'downloadableVersion', 'downloadableVersionTypes',
                'canonicalVersion', 'canonicalVersionTypes',
                'masterBrand', 'competitionWarning', 'competitionWarningProgrammeItem',
            ])
            ->from('ProgrammesPagesService:ProgrammeItem', 'p')
            ->leftJoin('p.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.competitionWarning', 'competitionWarning')
            ->leftJoin('competitionWarning.programmeItem', 'competitionWarningProgrammeItem')
            ->leftJoin('p.downloadableVersion', 'downloadableVersion')
            ->leftJoin('downloadableVersion.versionTypes', 'downloadableVersionTypes')
            ->leftJoin('p.streamableVersion', 'streamableVersion')
            ->leftJoin('streamableVersion.versionTypes', 'streamableVersionTypes')
            ->leftJoin('p.canonicalVersion', 'canonicalVersion')
            ->leftJoin('canonicalVersion.versionTypes', 'canonicalVersionTypes')
            ->where('p.id = :dbId')
            ->setParameter('dbId', $programmeItemDbId);

        $programmeArray = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
        if (empty($programmeArray)) {
            return $programmeArray;
        }
        return $this->hydrateProgrammeItems([$programmeArray])[0];
    }

    /**
     * Find for each clip the version that should be played out/linked to in playout
     *
     * @param int[] $programmeItemsDbId
     * @return array
     */
    public function findStreamableVersionForProgrammeItems(array $programmeItemsDbId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select([
                'p', 'streamableVersion', 'streamableVersionTypes',
                'masterBrand', 'competitionWarning', 'competitionWarningProgrammeItem',
            ])
            ->from('ProgrammesPagesService:ProgrammeItem', 'p')
            ->leftJoin('p.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.competitionWarning', 'competitionWarning')
            ->leftJoin('competitionWarning.programmeItem', 'competitionWarningProgrammeItem')
            ->innerJoin('p.streamableVersion', 'streamableVersion')
            ->innerJoin('streamableVersion.versionTypes', 'streamableVersionTypes')
            ->where('p.id IN (:dbIds)')
            ->setParameter('dbIds', $programmeItemsDbId);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return $this->hydrateProgrammeItems($result);
    }

    public function findDownloadableForProgrammesDescendantEpisodes(array $programmeItemsDbId, ?int $limit, int $offset): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select([
                'p', 'downloadableVersion', 'masterBrand', 'image', 'mbImage', 'network',
            ])
            ->from('ProgrammesPagesService:Episode', 'p')
            ->leftJoin('p.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('p.image', 'image')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->innerJoin('p.downloadableVersion', 'downloadableVersion')
            ->where('p.ancestry LIKE :ancestry')
            ->setParameter('ancestry', $this->ancestryIdsToString($programmeItemsDbId) . '%')
            ->addOrderBy('p.onDemandSortDate', 'DESC')
            ->addOrderBy('p.streamableFrom', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $withParents = $this->resolveProgrammeParents($result);
        return $this->hydrateProgrammeItems($withParents);
    }

    public function countDownloadableForProgrammesDescendantEpisodes(array $programmeItemsDbId): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(['COUNT(p)'])
            ->from('ProgrammesPagesService:Episode', 'p')
            ->where('p.ancestry LIKE :ancestry')
            ->andWhere('p.downloadableVersion IS NOT NULL')
            ->setParameter('ancestry', $this->ancestryIdsToString($programmeItemsDbId) . '%');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findDownloadableForGroupsDescendantEpisodes(int $programmeItemDbId, ?int $limit, int $offset): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select([
                'p', 'downloadableVersion', 'masterBrand', 'image', 'mbImage', 'network',
            ])
            ->from('ProgrammesPagesService:Episode', 'p')
            ->innerJoin('ProgrammesPagesService:Membership', 'membership', Query\Expr\Join::WITH, 'membership.memberCoreEntity = p')
            ->innerJoin('p.downloadableVersion', 'downloadableVersion')
            ->leftJoin('p.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('p.image', 'image')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->where('IDENTITY(membership.group) = :programmeItemDbId')
            ->addOrderBy('p.onDemandSortDate', 'DESC')
            ->addOrderBy('p.streamableFrom', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('programmeItemDbId', $programmeItemDbId);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $withParents = $this->resolveProgrammeParents($result);
        return $this->hydrateProgrammeItems($withParents);
    }

    public function countDownloadableForGroupsDescendantEpisodes(int $programmeItemDbId): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(['COUNT(p)'])
            ->from('ProgrammesPagesService:Episode', 'p')
            ->innerJoin('ProgrammesPagesService:Membership', 'membership', Query\Expr\Join::WITH, 'membership.memberCoreEntity = p')
            ->where('IDENTITY(membership.group) = :programmeItemDbId')
            ->andWhere('p.downloadableVersion IS NOT NULL')
            ->setParameter('programmeItemDbId', $programmeItemDbId);

        return $qb->getQuery()->getSingleScalarResult();
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

    private function resolveProgrammeParents(array $result)
    {
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        return $this->abstractResolveAncestry(
            $result,
            [$repo, 'coreEntityAncestryGetter']
        );
    }

    /**
     * This deals with the case where we want to query competition warnings
     * for a version where...
     * a) the version's programmeItem is not directly linked to a masterbrand, but
     * b) the programmeItem's ancestor is directly linked to a masterbrand.
     *
     * It ensures those things get the correct competition warning.
     *
     * In 90% of cases, this will just return the input array.
     *
     * In the other cases it queries all the programme's parents, and their masterbrands
     * joined to their competition warnings, adds them to the version, and returns that
     * whole mess.
     *
     * @param array $versions
     * @return array $versions
     */
    private function hydrateCompetitionWarnings(array $versions): array
    {
        $versionsToFetchFor = [];

        foreach ($versions as $index => $version) {
            if (isset($version['id']) && empty($version['programmeItem']['masterBrand'])) {
                $versionsToFetchFor[$index] = $version;
            }
        }
        if (empty($versionsToFetchFor)) {
            return $versions;
        }
        $versionsToFetchFor = $this->resolveProgrammeParentsForPlayout($versionsToFetchFor);
        foreach ($versionsToFetchFor as $index => $version) {
            $versions[$index] = $version;
        }
        return $versions;
    }

    /**
     *
     * This takes an array of programmeItems retrieved from the database, and
     * a) makes sure that the attached versions have the parent programmeItem as their explicit programmeItem (this
     *    ensures that all joins from that table to masterBrand and so forth are carried into the mappers and domain
     *    objects without extra queries/joins )
     * b) Makes sure that any streamableVersions get their competition warnings correctly set on any parent programmes
     *
     * @param array $programmeItems
     * @return array $programmeItems
     */
    private function hydrateProgrammeItems(array $programmeItems): array
    {
        $streamableVersions = [];
        foreach ($programmeItems as $index => &$programmeArray) {
            foreach (['streamableVersion', 'downloadableVersion', 'canonicalVersion'] as $key) {
                if (isset($programmeArray[$key])) {
                    // Mapping fails without this step. Creating more joins in SQL would be pointless, as we
                    // already know the programmeItem, so just hack it in here. Circular references never hurt anybody :-D
                    $programmeArray[$key]['programmeItem'] = $programmeArray;
                }
            }
            if (isset($programmeArray['streamableVersion'])) {
                $streamableVersions[$index] = $programmeArray['streamableVersion'];
            }
        }
        if (empty($streamableVersions)) {
            return $programmeItems;
        }
        $streamableVersions = $this->hydrateCompetitionWarnings($streamableVersions);
        foreach ($streamableVersions as $index => $streamableVersion) {
            $programmeItems[$index]['streamableVersion'] = $streamableVersions[$index];
        }
        return $programmeItems;
    }

    private function resolveProgrammeParentsForPlayout(array $result)
    {
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        return $this->abstractResolveAncestry(
            $result,
            [$repo, 'findByIdsForPlayout'],
            ['programmeItem', 'ancestry']
        );
    }
}
