<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Category;
use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeContainer;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CoreEntityMapper;
use InvalidArgumentException;

class ProgrammesService extends AbstractService
{
    private const ALL_VALID_ENTITY_TYPES = [
        'Programme',
        'ProgrammeContainer',
        'ProgrammeItem',
        'Brand',
        'Series',
        'Episode',
        'Clip',
    ];

    /** @var CoreEntityRepository */
    protected $repository;

    public function __construct(
        CoreEntityRepository $repository,
        CoreEntityMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function countAllTleosByCategory(Category $category, $ttl = CacheInterface::NORMAL): int
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $category->getId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($category) {
                return $this->repository->countTleosByCategory($category->getDbAncestryIds(), false);
            }
        );
    }

    /**
     * @return Programme[] types: Series|Episode|Brand
     */
    public function findAllTleosByCategory(
        Category $category,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $category->getId(), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($category, $limit, $page) {
                $offset = $this->getOffset($limit, $page);
                $programmesInSlice = $this->repository->findTleosByCategory(
                    $category->getDbAncestryIds(),
                    false,
                    $limit,
                    $offset
                );

                return $this->mapManyEntities($programmesInSlice);
            }
        );
    }

    public function countAvailableTleosByCategory(Category $category, $ttl = CacheInterface::NORMAL): int
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $category->getId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($category) {
                return $this->repository->countTleosByCategory($category->getDbAncestryIds(), true);
            }
        );
    }

    /**
     * @return Programme[] types: Series|Episode|Brand
     */
    public function findAvailableTleosByCategory(
        Category $category,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $category->getId(), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($category, $limit, $page) {
                $offset = $this->getOffset($limit, $page);
                $programmesInSlice = $this->repository->findTleosByCategory(
                    $category->getDbAncestryIds(),
                    true,
                    $limit,
                    $offset
                );

                return $this->mapManyEntities($programmesInSlice);
            }
        );
    }

    public function findAll(
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($limit, $page) {
                $dbEntities = $this->repository->findAllWithParents(
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    public function findChildrenSeriesByParent(
        ProgrammeContainer $container,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $container->getDbId(), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($container, $limit, $page) {
                $dbEntities = $this->repository->findChildrenSeriesByParent(
                    $container->getDbId(),
                    $limit,
                    $this->getOffset($limit, $page)
                );
                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    public function countAll(string $entityType = 'Programme', $ttl = CacheInterface::NORMAL): int
    {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);

        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $entityType, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($entityType) {
                return $this->repository->countAll($entityType);
            }
        );
    }

    public function findByPid(Pid $pid, string $entityType = 'Programme', $ttl = CacheInterface::NORMAL): ?Programme
    {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);

        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, (string) $pid, $entityType, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($pid, $entityType) {
                $dbEntity = $this->repository->findByPid($pid, $entityType);
                return $this->mapSingleEntity($dbEntity);
            }
        );
    }

    public function findByPidFull(Pid $pid, string $entityType = 'Programme', $ttl = CacheInterface::NORMAL): ?Programme
    {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);

        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, (string) $pid, $entityType, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($pid, $entityType) {
                $dbEntity = $this->repository->findByPidFull($pid, $entityType);
                return $this->mapSingleEntity($dbEntity);
            }
        );
    }

    public function findEpisodeGuideChildren(
        Programme $programme,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getDbId(), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $limit, $page) {
                $dbEntities = $this->repository->findEpisodeGuideChildren(
                    $programme->getDbId(),
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    public function countEpisodeGuideChildren(Programme $programme, $ttl = CacheInterface::NORMAL): int
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getDbId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme) {
                return $this->repository->countEpisodeGuideChildren($programme->getDbId());
            }
        );
    }

    public function findNextSiblingByProgramme(Programme $programme, $ttl = CacheInterface::NORMAL): ?Programme
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getDbId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme) {
                return $this->findSiblingByProgramme($programme, 'next');
            }
        );
    }

    public function findPreviousSiblingByProgramme(Programme $programme, $ttl = CacheInterface::NORMAL): ?Programme
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getDbId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme) {
                return $this->findSiblingByProgramme($programme, 'previous');
            }
        );
    }

    public function findDescendantsByPid(
        Pid $pid,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        // in order for this to be efficient, we need to know the original programme database ID.
        // @todo - investigate another way to do this so we don't need this effectively redundant query

        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, (string) $pid, $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($pid, $limit, $page) {

                $dbEntity = $this->repository->findByPidFull($pid);
                if (!$dbEntity) {
                    return null;
                }

                $dbEntities = $this->repository->findDescendants(
                    $dbEntity,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    public function countAvailableEpisodesByCategory(
        Category $category,
        $ttl = CacheInterface::NORMAL
    ): int {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $category->getId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($category) {
                return $this->repository->countAvailableEpisodesByCategoryAncestry(
                    $category->getDbAncestryIds()
                );
            }
        );
    }

    public function findAvailableEpisodesByCategory(
        Category $category,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $category->getId(), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($category, $limit, $page) {
                $dbEntities = $this->repository->findAvailableEpisodesByCategoryAncestry(
                    $category->getDbAncestryIds(),
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    public function searchByKeywords(
        string $keywords,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $keywords, $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($keywords, $limit, $page) {
                $dbEntities = $this->repository->findByKeywords(
                    $keywords,
                    false,
                    $limit,
                    $this->getOffset($limit, $page),
                    ['Brand', 'Series', 'Episode']
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    public function countByKeywords(string $keywords, $ttl = CacheInterface::NORMAL): int
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $keywords, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($keywords) {
                return $this->repository->countByKeywords($keywords, false, ['Brand', 'Series', 'Episode']);
            }
        );
    }

    public function searchAvailableByKeywords(
        string $keywords,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $keywords, $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($keywords, $limit, $page) {
                $dbEntities = $this->repository->findByKeywords(
                    $keywords,
                    true,
                    $limit,
                    $this->getOffset($limit, $page),
                    ['Brand', 'Series', 'Episode']
                );
                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    public function countAvailableByKeywords(string $keywords, $ttl = CacheInterface::NORMAL): int
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $keywords, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($keywords) {
                return $this->repository->countByKeywords($keywords, true, ['Brand', 'Series', 'Episode']);
            }
        );
    }

    protected function mapSingleEntity(?array $dbEntity, ...$additionalArgs)
    {
        if (is_null($dbEntity)) {
            return null;
        }

        return $this->mapper->getDomainModelForProgramme($dbEntity, ...$additionalArgs);
    }

    private function findSiblingByProgramme(Programme $programme, string $direction): ?Programme
    {
        // Programmes that don't have a parent can't have any siblings
        if (!$programme->getParent()) {
            return null;
        }

        // First check based on position
        if (!is_null($programme->getPosition())) {
            $dbEntity = $this->repository->findAdjacentProgrammeByPosition(
                $programme->getParent()->getDbId(),
                $programme->getPosition(),
                $this->dbType($programme),
                $direction
            );

            if ($dbEntity) {
                return $this->mapSingleEntity($dbEntity);
            }
        }

        if (!is_null($programme->getFirstBroadcastDate())) {
            $dbEntity = $this->repository->findAdjacentProgrammeByFirstBroadcastDate(
                $programme->getParent()->getDbId(),
                $programme->getFirstBroadcastDate(),
                $this->dbType($programme),
                $direction
            );

            if ($dbEntity) {
                return $this->mapSingleEntity($dbEntity);
            }
        }

        return null;
    }

    /**
     * A utility for returning the db type for a given Domain object
     */
    private function dbType(Programme $entity): ?string
    {
        if ($entity instanceof Brand) {
            return 'Brand';
        } elseif ($entity instanceof Series) {
            return 'Series';
        } elseif ($entity instanceof Episode) {
            return 'Episode';
        } elseif ($entity instanceof Clip) {
            return 'Clip';
        }
        return 'Unknown';
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
}
