<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Network;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Util\SearchUtilitiesTrait;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use DateTimeInterface;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;
use InvalidArgumentException;

class CoreEntityRepository extends MaterializedPathRepository
{
    use Traits\ParentTreeWalkerTrait;
    use SearchUtilitiesTrait;

    const ALL_VALID_ENTITY_TYPES = [
        'CoreEntity',
        'Programme',
        'ProgrammeContainer',
        'ProgrammeItem',
        'Brand',
        'Series',
        'Episode',
        'Clip',
        'Group',
        'Collection',
        'Gallery',
        'Season',
        'Franchise',
    ];

    const DB_GROUP_TYPES = ['collection', 'season', 'franchise', 'gallery'];

    private $ancestryCache = [];

    public function findTleosByCategories(
        array $categoryDbIds,
        bool $filterToAvailable,
        bool $orderByFirstBroadcast,
        ?int $limit,
        int $offset
    ): array {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(['DISTINCT programme', 'image', 'masterBrand', 'mbImage', 'network', 'nwImage'])
            ->from('ProgrammesPagesService:Programme', 'programme')
            ->leftJoin('programme.image', 'image')
            ->leftJoin('programme.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('network.image', 'nwImage')
            ->innerJoin('programme.categories', 'category')
            ->andWhere('programme INSTANCE OF (ProgrammesPagesService:Series, ProgrammesPagesService:Episode, ProgrammesPagesService:Brand)')
            ->andWhere('programme.parent IS NULL')
            // We use a list of categories obtained from a previous query rather than a simple WHERE clause
            // on the ancestry because the MySQL DB optimiser handles this much better, reducing this query's
            // execution time by an order of magnitude in some pathological cases
            ->andWhere('category.id IN (:dbids)')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('dbids', $categoryDbIds);

        if ($orderByFirstBroadcast) {
            $qb->orderBy('programme.firstBroadcastDate', 'DESC');
        }
        if ($filterToAvailable) {
            $qb->andWhere('programme.streamable = 1');
        }

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return $this->resolveParents($result);
    }

    public function countTleosByCategories(
        array $categoryDbIds,
        bool $filterToAvailable
    ): int {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(['COUNT(DISTINCT programme)'])
            ->from('ProgrammesPagesService:Programme', 'programme')
            ->innerJoin('programme.categories', 'category')
            ->andWhere('programme INSTANCE OF (ProgrammesPagesService:Series, ProgrammesPagesService:Episode, ProgrammesPagesService:Brand)')
            ->andWhere('programme.parent IS NULL')
            ->andWhere('category.id IN (:dbids)')
            ->setParameter('dbids', $categoryDbIds);

        if ($filterToAvailable) {
            $qb->andWhere('programme.streamable = 1');
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Return the count of available episodes given category ID's
     */
    public function countAvailableEpisodesByCategoryAncestry(array $ancestryDbIds): int
    {
        $ancestry = $this->ancestryIdsToString($ancestryDbIds);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(DISTINCT(episode.id))')
            ->from('ProgrammesPagesService:Episode', 'episode')
            ->innerJoin('episode.categories', 'category')
            ->andWhere('episode.streamable = 1')
            ->andWhere('category.ancestry LIKE :ancestry')
            ->setParameter('ancestry', $ancestry . '%'); // Availability DESC

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Return available episodes given category ID's
     */
    public function findAvailableEpisodesByCategoryAncestry(
        array $ancestryDbIds,
        ?int $limit,
        int $offset
    ): array {
        $ancestry = $this->ancestryIdsToString($ancestryDbIds);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('episode', 'image', 'masterBrand', 'network', 'mbImage')
            ->from('ProgrammesPagesService:Episode', 'episode')
            ->innerJoin('episode.categories', 'category')
            ->leftJoin('episode.image', 'image')
            ->leftJoin('episode.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->andWhere('episode.streamable = 1')
            ->andWhere('category.ancestry LIKE :ancestry')
            ->setParameter('ancestry', $ancestry . '%')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->addGroupBy('episode.id')
            ->addOrderBy('episode.streamableFrom', 'DESC')
            ->addOrderBy('episode.title');

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $this->resolveParents($result);
    }

    public function findByCoreEntityMembership(int $entityId, string $groupType, ?int $limit, int $offset): array
    {
        $this->assertEntityType($groupType, ['Group', 'Collection', 'Franchise', 'Gallery', 'Season']);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(['groupEntity', 'image', 'masterBrand', 'network', 'mbImage'])
            ->from('ProgrammesPagesService:' . $groupType, 'groupEntity')
            ->join('ProgrammesPagesService:Membership', 'membership', Query\Expr\Join::WITH, 'membership.group = groupEntity')
            ->leftJoin('groupEntity.image', 'image')
            ->leftJoin('groupEntity.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->where('IDENTITY(membership.memberCoreEntity) = :programmeId')
            ->groupBy('groupEntity.id')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->addOrderBy('groupEntity.pid', 'DESC')
            ->setParameter('programmeId', $entityId);

        $results = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $this->resolveParents($results);
    }

    /**
     * Get an entity, based upon its PID
     * Used when a page wants to find out about data related to an entity, but
     * doesn't need the fully hydrated entity itself.
     */
    public function findByPid(string $pid, string $entityType = 'CoreEntity'): ?array
    {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);

        $qText = <<<QUERY
SELECT entity
FROM ProgrammesPagesService:$entityType entity
WHERE entity.pid = :pid
QUERY;

        $q = $this->getEntityManager()->createQuery($qText)
            ->setParameter('pid', $pid);

        return $q->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    /**
     * Full Find By Pid
     *
     * This resolves all parents
     */
    public function findByPidFull(string $pid, string $entityType = 'CoreEntity'): ?array
    {
        $qb = $this->findFullCommon($entityType);
        $qb->andWhere('entity.pid = :pid')
            ->setParameter('pid', $pid);

        $result = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
        if (!$result) {
            return $result;
        }
        $this->addToAncestryCache([$result]);

        if (in_array($result['type'], self::DB_GROUP_TYPES)) {
            // If we have a group, we need to do something slightly different to hydrate the right info
            // Basically we do a findByPidFull on the parent (if set) and get the categories
            // etc on that.
            return $this->resolveGroupAncestry($result);
        }
        $withHydratedParents = $this->resolveParents([$result]);
        return $this->resolveCategories($withHydratedParents)[0];
    }

    /**
     * Full Find By Pid
     *
     * This resolves all parents
     */
    public function findByIdFull(int $dbId, string $entityType = 'CoreEntity'): ?array
    {
        $qb = $this->findFullCommon($entityType);
        $qb->andWhere('entity.id = :id')
            ->setParameter('id', $dbId);

        $result = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
        if (!$result) {
            return $result;
        }
        $this->addToAncestryCache([$result]);
        $withHydratedParents = $this->resolveParents([$result]);
        return $this->resolveCategories($withHydratedParents)[0];
    }

    /**
     * @param string[] $pids
     * @param string $entityType
     * @return array
     */
    public function findByPids(array $pids, string $entityType = 'CoreEntity'): array
    {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(['entity', 'image', 'masterBrand', 'network', 'mbImage', 'category', 'nwImage'])
            ->from('ProgrammesPagesService:' . $entityType, 'entity') // For filtering on type
            ->leftJoin('entity.image', 'image')
            ->leftJoin('entity.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('network.image', 'nwImage')
            ->leftJoin('entity.categories', 'category')
            ->andWhere('entity.pid IN (:pids)')
            ->setParameter('pids', $pids);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        // SQL makes no claims about ordering. This means it might return
        // entities in a different order to the list of PIDs that was passed in.
        // We want to force the ordering to be the same. e.g if you request pids
        // b0000003, b0000001, b0000002, you'll get back those enties in that
        // order (without this, SQL would probably go for alphanumeric ordering)
        $keyedResults = [];
        foreach ($result as $entity) {
            $keyedResults[$entity['pid']] = $entity;
        }

        $orderedResults = [];
        foreach ($pids as $pid) {
            // The entity for a pid may not be found if it doesn't exist, or if
            // it refers to an embargoed entity
            if (isset($keyedResults[$pid])) {
                $orderedResults[] = $keyedResults[$pid];
            }
        }

        return $this->resolveParents($orderedResults);
    }

    public function findByIds(array $ids): array
    {
        $results = $this->createQueryBuilder('programme')
            ->addSelect(['image', 'masterBrand', 'network', 'mbImage'])
            ->leftJoin('programme.image', 'image')
            ->leftJoin('programme.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->andWhere("programme.id IN(:ids)")
            ->setParameter('ids', $ids)
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $this->addToAncestryCache($results);
        return $results;
    }

    public function findByIdsForPlayout(array $ids): array
    {
        $results = $this->createQueryBuilder('programme')
            ->addSelect(['image', 'masterBrand', 'network', 'mbImage', 'competitionWarning', 'competitionWarningProgrammeItem'])
            ->leftJoin('programme.image', 'image')
            ->leftJoin('programme.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->leftJoin('masterBrand.competitionWarning', 'competitionWarning')
            ->leftJoin('competitionWarning.programmeItem', 'competitionWarningProgrammeItem')
            ->andWhere("programme.id IN(:ids)")
            ->setParameter('ids', $ids)
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $this->addToAncestryCache($results);
        return $results;
    }

    public function findDescendantsByType(array $ancestryDbIds, string $entityType, ?int $limit, int $offset) : array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
                   ->addSelect(['entity', 'masterBrand', 'image', 'mbImage', 'network'])
                   ->from('ProgrammesPagesService:' . $entityType, 'entity')
                   ->leftJoin('entity.masterBrand', 'masterBrand')
                   ->leftJoin('masterBrand.network', 'network')
                   ->leftJoin('entity.image', 'image')
                   ->leftJoin('masterBrand.image', 'mbImage')
                   ->andWhere('entity.ancestry LIKE :ancestry')
                   ->orderBy('entity.pid', 'DESC')
                   ->setFirstResult($offset)
                   ->setMaxResults($limit)
                   ->setParameter('ancestry', $this->ancestryIdsToString($ancestryDbIds) . '%');

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return $this->resolveParents($result);
    }

    /**
     * @param int[] $ancestryDbIds
     * @param string $entityType
     * @param int|null $limit
     * @param int $offset
     * @param bool $useOnDemandSort If true, sort by onDemandSortDate for onDemand column
     * @return array
     */
    public function findStreamableDescendantsByType(
        array $ancestryDbIds,
        string $entityType,
        ?int $limit,
        int $offset,
        bool $useOnDemandSort = false
    ) : array {
        $this->assertEntityType($entityType, ['Clip', 'Episode']);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->addSelect(['entity', 'masterBrand', 'image', 'mbImage', 'network'])
            ->from('ProgrammesPagesService:' . $entityType, 'entity')
            ->leftJoin('entity.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('entity.image', 'image')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->andWhere('entity.ancestry LIKE :ancestry')
            ->andWhere('entity.streamable = 1');
        if ($useOnDemandSort) {
            $qb->addOrderBy('entity.onDemandSortDate', 'DESC');
        }
        $qb->addOrderBy('entity.streamableFrom', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('ancestry', $this->ancestryIdsToString($ancestryDbIds) . '%');

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return $this->resolveParents($result);
    }

    public function countStreamableDescendantClips(array $ancestryDbIds)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('count(clip.id)')
            ->from('ProgrammesPagesService:Clip', 'clip')
            ->andWhere('clip.ancestry LIKE :ancestry')
            ->andWhere('clip.streamable = 1')
            ->setParameter('ancestry', $this->ancestryIdsToString($ancestryDbIds) . '%');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findChildrenSeriesByParent(
        int $id,
        ?int $limit,
        int $offset,
        bool $useDescendingOrder
    ): array {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->addSelect(['programme', 'image'])
            ->from('ProgrammesPagesService:Series', 'programme')
            ->leftJoin('programme.image', 'image')
            ->andWhere('programme.parent = :parentDbId')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $order = 'ASC';
        if ($useDescendingOrder) {
            $order = 'DESC';
        }

        $qb->addOrderBy('programme.position', $order)
            ->addOrderBy('programme.title', $order)
            ->setParameter('parentDbId', $id);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        if (!empty($result)) {
            $resolvedParent = $this->resolveParents([$result[0]]);

            foreach ($result as &$res) {
                $res['parent'] = $resolvedParent[0]['parent'];
            }
        }

        return $result;
    }

    public function findChildrenSeriesWithClipsByParent(
        int $id,
        ?int $limit,
        int $offset,
        bool $useDescendingOrder
    ): array {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->addSelect(['programme', 'image'])
            ->from('ProgrammesPagesService:Series', 'programme')
            ->leftJoin('programme.image', 'image')
            ->andWhere('programme.parent = :parentDbId')
            ->andWhere('programme.availableClipsCount > 0')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $order = 'ASC';
        if ($useDescendingOrder) {
            $order = 'DESC';
        }

        $qb->addOrderBy('programme.position', $order)
            ->addOrderBy('programme.title', $order)
            ->setParameter('parentDbId', $id);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        if (!empty($result)) {
            $resolvedParent = $this->resolveParents([$result[0]]);

            foreach ($result as &$res) {
                $res['parent'] = $resolvedParent[0]['parent'];
            }
        }

        return $result;
    }

    public function findAllWithParents(?int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('programme')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return $this->resolveParents($result);
    }

    public function countAll(string $entityType = 'CoreEntity'): int
    {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('count(entity.id)')
            ->from('ProgrammesPagesService:' . $entityType, 'entity');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findEpisodeGuideChildren(int $dbId, ?int $limit, int $offset): array
    {
        // Note the ORDER BY in this query uses DESC in everything so that the covering
        // index core_entity_children_json_cover_idx can be used. Mixing ASC and DESC prevents MySQL
        // using an index
        $qText = <<<QUERY
SELECT programme, image, masterBrand, network, mbImage
FROM ProgrammesPagesService:Programme programme
LEFT JOIN programme.image image
LEFT JOIN programme.masterBrand masterBrand
LEFT JOIN masterBrand.network network
LEFT JOIN masterBrand.image mbImage
WHERE programme.parent = :dbId
AND programme INSTANCE OF (ProgrammesPagesService:Series, ProgrammesPagesService:Episode)
ORDER BY programme.position DESC, programme.firstBroadcastDate DESC, programme.title DESC
QUERY;

        $q = $this->getEntityManager()->createQuery($qText)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('dbId', $dbId);

        $result = $q->getResult(Query::HYDRATE_ARRAY);
        return $this->resolveParents($result);
    }

    public function countEpisodeGuideChildren(int $dbId): int
    {
        $qText = <<<QUERY
SELECT count(programme.id)
FROM ProgrammesPagesService:Programme programme
WHERE programme.parent = :dbId
AND programme INSTANCE OF (ProgrammesPagesService:Series, ProgrammesPagesService:Episode)
QUERY;

        $q = $this->getEntityManager()->createQuery($qText)
            ->setParameter('dbId', $dbId);

        return $q->getSingleScalarResult();
    }

    public function findAdjacentProgrammeByPosition(
        int $parentDbId,
        int $position,
        string $entityType,
        string $direction
    ): ?array {
        $this->assertEntityType($entityType, ['Series', 'Episode', 'Clip']);

        if (!in_array($direction, ['next', 'previous'])) {
            throw new InvalidArgumentException(sprintf(
                'Called findAdjacentProgrammeByPosition with an invalid direction type. Expected one of "%s" or "%s" but got "%s"',
                'next',
                'previous',
                $direction
            ));
        }

        $isNext = $direction == 'next';
        $orderDirection = $isNext ? 'ASC' : 'DESC';
        $filterOperation = $isNext ? '>' : '<' ;

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(['programme', 'masterbrand', 'network'])
            ->from('ProgrammesPagesService:' . $entityType, 'programme')
            ->leftJoin('programme.masterBrand', 'masterbrand')
            ->leftJoin('masterbrand.network', 'network')
            ->andWhere('programme.parent = :parentDbId')
            ->andWhere('programme.position ' . $filterOperation . ' :originalPosition')
            ->orderBy('programme.position', $orderDirection)
            ->addOrderBy('programme.pid', $orderDirection)
            ->setMaxResults(1)
            ->setParameter('parentDbId', $parentDbId)
            ->setParameter('originalPosition', $position);

        $result = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);

        if (!$result) {
            return $result;
        }

        return $this->resolveParents([$result])[0];
    }

    public function findAdjacentProgrammeItemByReleaseDate(
        int $parentDbId,
        PartialDate $releaseDate,
        string $entityType,
        string $direction
    ): ?array {
        $this->assertEntityType($entityType, ['Episode', 'Clip']);

        if (!in_array($direction, ['next', 'previous'])) {
            throw new InvalidArgumentException(sprintf(
                'Called findAdjacentProgrammeItemByReleaseDate with an invalid direction type. Expected one of "%s" or "%s" but got "%s"',
                'next',
                'previous',
                $direction
            ));
        }

        $isNext = $direction == 'next';
        $orderDirection = $isNext ? 'ASC' : 'DESC';
        $filterOperation = $isNext ? '>' : '<' ;

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(['programme'])
            ->from('ProgrammesPagesService:' . $entityType, 'programme')
            ->andWhere('programme.parent = :parentDbId')
            ->andWhere('programme.releaseDate ' . $filterOperation . ' :originalReleaseDate')
            ->orderBy('programme.releaseDate', $orderDirection)
            ->setMaxResults(1)
            ->setParameter('parentDbId', $parentDbId)
            ->setParameter('originalReleaseDate', $releaseDate);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findAdjacentProgrammeByFirstBroadcastDate(
        int $parentDbId,
        DateTimeInterface $firstBroadcastDate,
        string $entityType,
        string $direction
    ): ?array {
        if (!in_array($direction, ['next', 'previous'])) {
            throw new InvalidArgumentException(sprintf(
                'Called findAdjacentProgrammeItemByReleaseDate with an invalid direction type. Expected one of "%s" or "%s" but got "%s"',
                'next',
                'previous',
                $direction
            ));
        }

        $isNext = $direction == 'next';
        $orderDirection = $isNext ? 'ASC' : 'DESC';
        $filterOperation = $isNext ? '>' : '<' ;

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(['programme', 'masterbrand', 'network'])
            ->from('ProgrammesPagesService:' . $entityType, 'programme')
            ->leftJoin('programme.masterBrand', 'masterbrand')
            ->leftJoin('masterbrand.network', 'network')
            ->andWhere('programme.parent = :parentDbId')
            ->andWhere('programme.firstBroadcastDate ' . $filterOperation . ' :firstBroadcastDate')
            ->orderBy('programme.firstBroadcastDate', $orderDirection)
            ->setMaxResults(1)
            ->setParameter('parentDbId', $parentDbId)
            ->setParameter('firstBroadcastDate', $firstBroadcastDate);

        $result = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);

        if (!$result) {
            return $result;
        }

        return $this->resolveParents([$result])[0];
    }

    public function countByGroup(int $groupDbId): int
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->addSelect(['COUNT(entity)'])
            ->from('ProgrammesPagesService:CoreEntity', 'entity')
            ->innerJoin('ProgrammesPagesService:Membership', 'membership', Query\Expr\Join::WITH, 'membership.memberCoreEntity = entity')
            ->where('membership.group = :groupId')
            ->setParameter('groupId', $groupDbId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByGroup(
        int $groupDbId,
        ?int $limit,
        int $offset
    ): array {
        $qb = $this->createQueryBuilder('entity')
            ->addSelect(['image', 'masterBrand', 'network', 'mbImage', 'nwImage'])
            ->innerJoin('ProgrammesPagesService:Membership', 'membership', Query\Expr\Join::WITH, 'membership.memberCoreEntity = entity')
            ->leftJoin('entity.image', 'image')
            ->leftJoin('entity.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('network.image', 'nwImage')
            ->where('membership.group = :groupId')
            ->orderBy('membership.position', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('groupId', $groupDbId);

        $results = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $this->resolveParents($results);
    }

    public function findStreamableUnderGroup(
        int $groupDbId,
        string $entityType,
        ?int $limit,
        int $offset
    ): array {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);
        /**
         * We're using native queries here due to Doctrine's lack of support for UNION and making certain types of
         * subquery difficult. We use the ResultSetMappingBuilder to get the result from the native query into the
         * format Doctrine usually returns to our mappers
         */
        $rsmb = new Query\ResultSetMappingBuilder($this->getEntityManager(), Query\ResultSetMappingBuilder::COLUMN_RENAMING_INCREMENT);
        $rsmb->addRootEntityFromClassMetadata(Episode::class, 'core_entity', ['type' => 'magic_type']);
        /**
         * Little bit of magic here to override doctrine's handling of the magic "type" field on the CoreEntity MappedSuperClass
         * This makes the "type" field available in the array doctrine returns so our mappers can correctly identify the core
         * entity type.
         */
        $rsmb->addMetaResult('core_entity', 'magic_type', 'type', true, 'string');
        $rsmb->addJoinedEntityFromClassMetadata(MasterBrand::class, 'masterBrand', 'core_entity', 'masterBrand');
        $rsmb->addJoinedEntityFromClassMetadata(Image::class, 'image', 'core_entity', 'image');
        $rsmb->addJoinedEntityFromClassMetadata(Image::class, 'mbImage', 'masterBrand', 'image');
        $rsmb->addJoinedEntityFromClassMetadata(Network::class, 'network', 'masterBrand', 'network');
        $rsmb->addJoinedEntityFromClassMetadata(Image::class, 'nwImage', 'network', 'image');
        // Map raw SQL table aliases to doctrine aliases and have doctrine make the SELECT part of the query
        $selectClause = $rsmb->generateSelectClause([
            'core_entity' => 'ce',
            'image' => 'ci',
            'masterBrand' => 'mb',
            'mbImage' => 'mi',
            'network' => 'n',
            'nwImage' => 'ni',
        ]);
        $sql = 'SELECT ' . $selectClause ;
        $sql .= <<<'EOQ'
            FROM core_entity ce
            LEFT JOIN image ci ON ce.image_id = ci.id
            LEFT JOIN master_brand mb ON ce.master_brand_id = mb.id
            LEFT JOIN image mi ON mb.image_id = mi.id
            LEFT JOIN network n ON mb.network_id = n.id
            LEFT JOIN image ni ON n.image_id = ni.id
            WHERE
            ce.id IN (SELECT a.id FROM ( 
                (SELECT ct.id FROM core_entity ct INNER JOIN membership m1 ON ct.tleo_id = m1.member_core_entity_id WHERE m1.group_id = :groupDbId AND ct.type=:ceType)
                UNION
                (SELECT cp.id FROM core_entity cp INNER JOIN membership m2 ON cp.parent_id = m2.member_core_entity_id WHERE m2.group_id = :groupDbId AND cp.type=:ceType)
                UNION
                (SELECT cc.id FROM core_entity cc INNER JOIN membership m3 ON cc.id = m3.member_core_entity_id WHERE m3.group_id = :groupDbId AND cc.type=:ceType)
                ) AS a)
            AND ce.streamable = 1
            AND ce.is_embargoed = 0
            ORDER BY ce.on_demand_sort_date DESC
            LIMIT :limit
            OFFSET :offset
EOQ;
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsmb);
        $query->setParameter('groupDbId', $groupDbId)
            ->setParameter('ceType', strtolower($entityType))
            ->setParameter('limit', $limit)
            ->setParameter('offset', $offset);

        $result = $query->getResult(AbstractQuery::HYDRATE_ARRAY);
        return $this->resolveParents($result);
    }


    public function countByKeywords(
        string $keywords,
        bool $filterAvailable,
        ?array $entityTypes
    ): int {
        $booleanKeywords = $this->makeBooleanSearchQuery($keywords);
        if (!$booleanKeywords) {
            // Don't bother searching empty/invalid terms
            return 0;
        }

        $qText = <<<QUERY
SELECT COUNT(coreEntity.id)
FROM ProgrammesPagesService:CoreEntity coreEntity
WHERE MATCH_AGAINST (coreEntity.searchTitle, coreEntity.shortSynopsis, :booleanKeywords 'IN BOOLEAN MODE') > 0
QUERY;
        if ($filterAvailable) {
            $qText .= ' AND coreEntity.streamable = 1';
        }

        if ($entityTypes) {
            $qText .= ' AND (' . $this->makeEntityTypesDQL($entityTypes, 'coreEntity') . ')';
        }

        $q = $this->getEntityManager()->createQuery($qText)
            ->setParameter('booleanKeywords', $booleanKeywords);

        $count = $q->getSingleScalarResult();
        return $count ? $count : 0;
    }

    public function findByKeywords(
        string $inputKeywords,
        bool $filterAvailable,
        ?int $limit,
        int $offset,
        ?array $entityTypes
    ): array {
        $keywords = $this->stripPunctuation($inputKeywords);
        $booleanKeywords = $this->makeBooleanSearchQuery($inputKeywords);
        if (!$booleanKeywords) {
            // Don't bother searching empty/invalid terms
            return [];
        }

        $qText = <<<QUERY
SELECT coreEntity, image, masterBrand, network, mbImage,
(
    (  (MATCH_AGAINST (coreEntity.searchTitle, :keywords ) * 3)
      + (MATCH_AGAINST (coreEntity.searchTitle, coreEntity.shortSynopsis, :keywords ) * 1)
      + (MATCH_AGAINST (coreEntity.searchTitle, :quotedKeywords ) * 7)
      + ( CASE WHEN (coreEntity.searchTitle=:keywords) THEN 100 ELSE 1 END )
    )
    * ( CASE WHEN ((coreEntity INSTANCE OF (ProgrammesPagesService:Brand, ProgrammesPagesService:Series)) AND coreEntity.parent IS NULL) THEN 5 ELSE 1 END)
) AS HIDDEN rel
FROM ProgrammesPagesService:CoreEntity coreEntity
LEFT JOIN coreEntity.image image
LEFT JOIN coreEntity.masterBrand masterBrand
LEFT JOIN masterBrand.network network
LEFT JOIN masterBrand.image mbImage
WHERE MATCH_AGAINST (coreEntity.searchTitle, coreEntity.shortSynopsis, :booleanKeywords 'IN BOOLEAN MODE') > 0
QUERY;
        if ($filterAvailable) {
            $qText .= ' AND coreEntity.streamable = 1';
        }
        if ($entityTypes) {
            $qText .= ' AND (' . $this->makeEntityTypesDQL($entityTypes, 'coreEntity') . ')';
        }

        $qText .= ' ORDER BY rel DESC';

        $q = $this->getEntityManager()->createQuery($qText)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('keywords', $keywords)
            ->setParameter('booleanKeywords', $booleanKeywords)
            ->setParameter('quotedKeywords', '"' . $keywords . '"');

        return $this->resolveParents($q->getResult(Query::HYDRATE_ARRAY));
    }

    public function clearAncestryCache(): void
    {
        $this->ancestryCache = [];
    }

    public function coreEntityAncestryGetter(array $ids): array
    {
        $cached = [];
        foreach ($ids as $index => $id) {
            if (!isset($this->ancestryCache[$id])) {
                // If any of our ancestors is not in the cache, just do the query
                return $this->findByIds($ids);
            }
            $cached[] = $this->ancestryCache[$id];
        }
        // Return cached ancestors, saving a query
        return $cached;
    }

    private function findFullCommon(string $entityType): QueryBuilder
    {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);

        // YIKES! categories is a many-to-many join, that could result in
        // an increase of rows returned by the DB and the potential for slow DB
        // queries as per https://ocramius.github.io/blog/doctrine-orm-optimization-hydration/.
        // Except it doesn't - the majority of Programmes have less than 3
        // categories. At time of writing this comment (June 2016) only 9% of
        // the Programmes in PIPS have 3 or more Categories and the most
        // Categories a Programme has is 12. Creating an few extra rows in
        // rare-ish cases is way more efficient that having to do a two-step
        // hydration process.
        // We need to JOIN to masterBrand's image as the image hierarchy can
        // fall back to the masterbrand's image if no masterbrand exists in the
        // existing image hierarchy

        return $this->getEntityManager()->createQueryBuilder()
            ->select(['entity', 'image', 'masterBrand', 'network', 'mbImage', 'category', 'nwImage'])
            ->from('ProgrammesPagesService:' . $entityType, 'entity')// For filtering on type
            ->leftJoin('entity.image', 'image')
            ->leftJoin('entity.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('network.image', 'nwImage')
            ->leftJoin('entity.categories', 'category');
    }

    private function resolveParents(array $programmes): array
    {
        return $this->abstractResolveAncestry(
            $programmes,
            [$this, 'coreEntityAncestryGetter']
        );
    }

    private function resolveCategories(array $programmes): array
    {
        return $this->abstractResolveNestedAncestry(
            $programmes,
            'categories',
            [$this, 'categoryAncestryGetter']
        );
    }

    private function resolveGroupAncestry(array $group): array
    {
        $ancestorIds = $this->getParentIdsFromAncestry($group['ancestry']);
        $group['parent'] = null;
        $parentId = end($ancestorIds);
        if ($parentId) {
            $group['parent'] = $this->findByIdFull($parentId, 'Programme');
        }
        return $group;
    }

    private function categoryAncestryGetter(array $ids): array
    {
        /** @var CategoryRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Category');
        return $repo->findByIds($ids);
    }

    private function assertEntityType(?string $entityType, array $validEntityTypes): void
    {
        if (!in_array($entityType, $validEntityTypes)) {
            throw new InvalidArgumentException(sprintf(
                'Called %s with an invalid type. Expected one of %s but got "%s"',
                debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'],
                '"' . implode('", "', $validEntityTypes) . '"',
                $entityType
            ));
        }
    }

    private function makeEntityTypesDQL(?array $entityTypes, string $alias)
    {
        if (empty($entityTypes)) {
            return '';
        }

        foreach ($entityTypes as $entityType) {
            $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);
        }

        foreach ($entityTypes as &$entityType) {
            $entityType = 'ProgrammesPagesService:' . $entityType;
        }

        return " ($alias INSTANCE OF (" . join(',', $entityTypes) . "))";
    }

    /**
     * Be very careful what you pass into this. It must be an array
     * of CoreEntity with all of the joins in findById or bad things
     * will happen.
     *
     * @param array $results
     */
    private function addToAncestryCache(array $results)
    {
        foreach ($results as $result) {
            if (isset($result['id'])) {
                $this->ancestryCache[$result['id']] = $result;
            }
        }
    }
}
