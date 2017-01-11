<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Util\StripPunctuationTrait;
use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use Doctrine\ORM\Query;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;
use InvalidArgumentException;
use DateTimeInterface;

class CoreEntityRepository extends MaterializedPathRepository
{
    use Traits\ParentTreeWalkerTrait;
    use Traits\SetLimitTrait;
    use StripPunctuationTrait;

    const ALL_VALID_ENTITY_TYPES = [
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
        'CoreEntity',
    ];

    public function findTleosByCategory(
        array $ancestryDbIds,
        bool $filterToAvailable,
        $medium,
        $limit,
        int $offset
    ) {
        $qb = $this->getEntityManager()->createQueryBuilder()
                   ->select(['DISTINCT programme', 'image', 'masterbrand', 'mbImage'])
                   ->from('ProgrammesPagesService:Programme', 'programme')
                   ->leftJoin('programme.image', 'image')
                   ->leftJoin('programme.masterBrand', 'masterbrand')
                   ->leftJoin('masterbrand.image', 'mbImage')
                   ->innerJoin('programme.categories', 'category')
                   ->andWhere('programme INSTANCE OF (ProgrammesPagesService:Series, ProgrammesPagesService:Episode, ProgrammesPagesService:Brand)')
                   ->andWhere('programme.parent IS NULL')
                   ->andWhere('category.ancestry LIKE :ancestry')
                   ->orderBy('programme.title', 'ASC')
                   ->addOrderBy('programme.pid', 'ASC')
                   ->setFirstResult($offset)
                   ->setParameter('ancestry', $this->ancestryIdsToString($ancestryDbIds) . '%');

        if ($filterToAvailable) {
            $qb->andWhere('programme.streamable = 1');
        }

        if (!is_null($medium)) {
            $this->assertNetworkMedium($medium);

            $qb->innerJoin('masterbrand.network', 'network')
               ->andWhere('network.medium = :medium')
               ->setParameter('medium', $medium);
        }

        $qb = $this->setLimit($qb, $limit);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * Return the count of available episodes given category ID's
     *
     * @param array       $ancestryDbIds
     * @param string|null $medium
     *
     * @return int
     */
    public function countAvailableEpisodesByCategoryAncestry(
        array $ancestryDbIds,
        $medium
    ): int {
        $ancestry = $this->ancestryIdsToString($ancestryDbIds);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(DISTINCT(episode.id))')
            ->from('ProgrammesPagesService:Episode', 'episode')
            ->innerJoin('episode.categories', 'category')
            ->andWhere('episode.streamable = 1')
            ->andWhere('category.ancestry LIKE :ancestry')
            ->setParameter('ancestry', $ancestry . '%'); // Availability DESC

        if ($medium) {
            $this->assertNetworkMedium($medium);

            $qb->innerJoin('episode.masterBrand', 'masterBrand')
                ->innerJoin('masterBrand.network', 'network')
                ->andWhere('network.medium = :medium')
                ->setParameter('medium', $medium);
        }

        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }

    /**
     * Return available episodes given category ID's
     *
     * @param array       $ancestryDbIds
     * @param string|null $medium
     * @param int|null    $limit
     * @param int         $offset
     *
     * @return array
     */
    public function findAvailableEpisodesByCategoryAncestry(
        array $ancestryDbIds,
        $medium,
        $limit,
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
            ->addGroupBy('episode.id')
            ->addOrderBy('episode.streamableFrom', 'DESC')
            ->addOrderBy('episode.title');

        if ($medium) {
            $this->assertNetworkMedium($medium);

            $qb->andWhere('network.medium = :medium')
                ->setParameter('medium', $medium);
        }

        $qb = $this->setLimit($qb, $limit);
        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $this->resolveParents($result);
    }

    /**
     * Get an entity, based upon its PID
     * Used when a page wants to find out about data related to an entity, but
     * doesn't need the fully hydrated entity itself.
     *
     * @param string $pid        The pid to lookup
     * @param string $entityType Filter results by "Programme", "Group" or "CoreEntity"
     * @return Programme|Group|null
     */
    public function findByPid(string $pid, string $entityType = 'CoreEntity')
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
     *
     * @param string $pid        The pid to lookup
     * @param string $entityType Filter results by "Programme", "Group" or "CoreEntity" to not filter
     * @return array|null
     */
    public function findByPidFull(string $pid, string $entityType = 'CoreEntity')
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

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(['entity', 'image', 'masterBrand', 'network', 'mbImage', 'category'])
            ->from('ProgrammesPagesService:' . $entityType, 'entity') // For filtering on type
            ->leftJoin('entity.image', 'image')
            ->leftJoin('entity.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->leftJoin('entity.categories', 'category')
            ->where('entity.pid = :pid')
            ->setParameter('pid', $pid);

        $result = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
        $withHydratedParents = $result ? $this->resolveParents([$result])[0] : $result;
        return $withHydratedParents ? $this->resolveCategories([$withHydratedParents])[0] : $withHydratedParents;
    }

    public function findByIds(array $ids)
    {
        return $this->createQueryBuilder('programme')
            ->addSelect(['image', 'masterBrand', 'network', 'mbImage'])
            ->leftJoin('programme.image', 'image')
            ->leftJoin('programme.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->where("programme.id IN(:ids)")
            ->setParameter('ids', $ids)
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function findChildrenSeriesByParent(int $id, $limit, int $offset)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->addSelect(['programme', 'image'])
            ->from('ProgrammesPagesService:Series', 'programme')
            ->leftJoin('programme.image', 'image')
            ->andWhere('programme.parent = :parentDbId')
            ->addOrderBy('programme.position', 'ASC')
            ->addOrderBy('programme.title', 'ASC')
            ->setFirstResult($offset)
            ->setParameter('parentDbId', $id);

        $qb = $this->setLimit($qb, $limit);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        if (!empty($result)) {
            $resolvedParent = $this->resolveParents([$result[0]]);

            foreach ($result as &$res) {
                $res['parent'] = $resolvedParent[0]['parent'];
            }
        }

        return $result;
    }

    /**
     * @param int|AbstractService::NO_LIMIT $limit
     * @param int $offset
     * @return array
     */
    public function findAllWithParents($limit, int $offset)
    {
        $qb = $this->createQueryBuilder('programme')
            ->setFirstResult($offset);

        $qb = $this->setLimit($qb, $limit);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return $this->resolveParents($result);
    }

    public function countAll()
    {
        $qb = $this->createQueryBuilder('programme')
            ->select(['count(programme.id)']);
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param int $dbId
     * @param int|AbstractService::NO_LIMIT $limit
     * @param int $offset
     * @return array
     */
    public function findEpisodeGuideChildren($dbId, $limit, int $offset)
    {
        $qText = <<<QUERY
SELECT programme, image, masterBrand, network, mbImage
FROM ProgrammesPagesService:Programme programme
LEFT JOIN programme.image image
LEFT JOIN programme.masterBrand masterBrand
LEFT JOIN masterBrand.network network
LEFT JOIN masterBrand.image mbImage
WHERE programme.parent = :dbId
AND programme INSTANCE OF (ProgrammesPagesService:Series, ProgrammesPagesService:Episode)
ORDER BY programme.position DESC, programme.firstBroadcastDate DESC, programme.title ASC, programme.pid ASC
QUERY;

        $q = $this->getEntityManager()->createQuery($qText)
            ->setFirstResult($offset)
            ->setParameter('dbId', $dbId);

        $q = $this->setLimit($q, $limit);

        $result = $q->getResult(Query::HYDRATE_ARRAY);
        return $this->resolveParents($result);
    }

    public function countEpisodeGuideChildren($dbId)
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
    ) {
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
            ->select(['programme'])
            ->from('ProgrammesPagesService:' . $entityType, 'programme')
            ->andWhere('programme.parent = :parentDbId')
            ->andWhere('programme.position ' . $filterOperation . ' :originalPosition')
            ->orderBy('programme.position', $orderDirection)
            ->addOrderBy('programme.pid', $orderDirection)
            ->setMaxResults(1)
            ->setParameter('parentDbId', $parentDbId)
            ->setParameter('originalPosition', $position);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findAdjacentProgrammeItemByReleaseDate(
        int $parentDbId,
        PartialDate $releaseDate,
        string $entityType,
        string $direction
    ) {
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
    ) {
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
            ->andWhere('programme.firstBroadcastDate ' . $filterOperation . ' :firstBroadcastDate')
            ->orderBy('programme.firstBroadcastDate', $orderDirection)
            ->setMaxResults(1)
            ->setParameter('parentDbId', $parentDbId)
            ->setParameter('firstBroadcastDate', $firstBroadcastDate);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    /**
     * @param Programme $programme
     * @param int|AbstractService::NO_LIMIT $limit
     * @param int $offset
     * @return array
     */
    public function findDescendants($programme, $limit, int $offset)
    {
        $qb = $this->getChildrenQueryBuilder($programme)
            ->setFirstResult($offset);

        $qb = $this->setLimit($qb, $limit);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $this->resolveParents($result);
    }

    public function countByKeywords(
        string $keywords,
        $networkMedium,
        bool $filterAvailable,
        array $entityTypes = null
    ): int {
        $keywords = $this->stripPunctuation($keywords);
        $booleanKeywords = join(' +', explode(' ', $keywords));
        $booleanKeywords = '+' . $booleanKeywords;

        $qText = <<<QUERY
SELECT COUNT(coreEntity.id)
FROM ProgrammesPagesService:CoreEntity coreEntity
QUERY;
        if ($networkMedium) {
            $qText .= <<<QUERY
 JOIN coreEntity.masterBrand masterBrand
JOIN masterBrand.network network
QUERY;
        }
        $qText .= <<<QUERY
 WHERE MATCH_AGAINST (coreEntity.searchTitle, coreEntity.shortSynopsis, :booleanKeywords 'IN BOOLEAN MODE') > 0
QUERY;
        if ($filterAvailable) {
            $qText .= ' AND coreEntity.streamable = 1';
        }

        if ($entityTypes) {
            $qText .= ' AND (' . $this->makeEntityTypesDQL($entityTypes, 'coreEntity') . ')';
        }

        if ($networkMedium) {
            if (in_array($networkMedium, [NetworkMediumEnum::RADIO, NetworkMediumEnum::TV])) {
                $qText .= ' AND network.medium = :service';
            } else {
                throw new \InvalidArgumentException('Network medium must be tv or radio');
            }
        }
        $q = $this->getEntityManager()->createQuery($qText)
            ->setParameter('booleanKeywords', $booleanKeywords);

        if ($networkMedium) {
            $q->setParameter('service', $networkMedium);
        }

        $count = $q->getSingleScalarResult();
        return $count ? $count : 0;
    }

    /**
     * @param string $keywords
     * @param string $networkMedium
     * @param bool $filterAvailable
     * @param int|AbstractService::NO_LIMIT $limit
     * @param int $offset
     * @param array $entityTypes
     * @return array
     */
    public function findByKeywords(
        string $keywords,
        $networkMedium,
        bool $filterAvailable,
        $limit,
        int $offset,
        array $entityTypes = null
    ): array {
        $keywords = $this->stripPunctuation($keywords);
        $booleanKeywords = join(' +', explode(' ', $keywords));
        $booleanKeywords = '+' . $booleanKeywords;

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
        if ($networkMedium) {
            if (in_array($networkMedium, [NetworkMediumEnum::RADIO, NetworkMediumEnum::TV])) {
                $qText .= ' AND network.medium = :service';
            } else {
                throw new InvalidArgumentException(
                    sprintf('Network medium must be tv or radio, got %s instead', $networkMedium)
                );
            }
        }
        $qText .= ' ORDER BY rel DESC';

        $q = $this->getEntityManager()->createQuery($qText)
            ->setFirstResult($offset)
            ->setParameter('keywords', $keywords)
            ->setParameter('booleanKeywords', $booleanKeywords)
            ->setParameter('quotedKeywords', '"' . $keywords . '"');

        if ($networkMedium) {
            $q->setParameter('service', $networkMedium);
        }
        $q = $this->setLimit($q, $limit);

        return $q->getResult(Query::HYDRATE_ARRAY);
    }

    private function resolveParents(array $programmes)
    {
        return $this->abstractResolveAncestry(
            $programmes,
            [$this, 'programmeAncestryGetter']
        );
    }

    private function programmeAncestryGetter(array $ids)
    {
        return $this->findByIds($ids);
    }

    private function resolveCategories(array $programmes)
    {
        return $this->abstractResolveNestedAncestry(
            $programmes,
            'categories',
            [$this, 'categoryAncestryGetter']
        );
    }

    private function categoryAncestryGetter(array $ids)
    {
        /** @var CategoryRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Category');
        return $repo->findByIds($ids);
    }

    private function ancestryIdsToString(array $ancestry)
    {
        return implode(',', $ancestry) . ',';
    }

    private function assertEntityType($entityType, $validEntityTypes)
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

    private function assertNetworkMedium(string $medium)
    {
        if (!in_array($medium, [NetworkMediumEnum::TV, NetworkMediumEnum::RADIO])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Network medium must be %s or %s, instead got %s',
                    NetworkMediumEnum::TV,
                    NetworkMediumEnum::RADIO,
                    $medium
                )
            );
        }
    }

    private function makeEntityTypesDQL(array $entityTypes, string $alias)
    {
        foreach ($entityTypes as $entityType) {
            $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);
        }
        if (empty($entityTypes)) {
            return '';
        }
        foreach ($entityTypes as &$entityType) {
            $entityType = 'ProgrammesPagesService:' . $entityType;
        }
        return " ($alias INSTANCE OF (" . join(',', $entityTypes) . "))";
    }
}
