<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Category;
use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeContainer;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper;
use InvalidArgumentException;
use Psr\Cache\CacheItemPoolInterface;

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
        ProgrammeMapper $mapper,
        CacheItemPoolInterface $cacheItemPoolInterface
    ) {
        parent::__construct($repository, $mapper, $cacheItemPoolInterface);
    }

    public function countAllTleosByCategory(Category $category): int
    {
        return $this->repository->countTleosByCategory($category->getDbAncestryIds(), false);
    }

    /**
     * @return Programme[] types: Series|Episode|Brand
     */
    public function findAllTleosByCategory(
        Category $category,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $offset = $this->getOffset($limit, $page);
        $programmesInSlice = $this->repository->findTleosByCategory(
            $category->getDbAncestryIds(),
            false,
            $limit,
            $offset
        );

        return $this->mapManyEntities($programmesInSlice);
    }

    public function countAvailableTleosByCategory(Category $category): int
    {
        return $this->repository->countTleosByCategory($category->getDbAncestryIds(), true);
    }

    /**
     * @return Programme[] types: Series|Episode|Brand
     */
    public function findAvailableTleosByCategory(
        Category $category,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $offset = $this->getOffset($limit, $page);
        $programmesInSlice = $this->repository->findTleosByCategory(
            $category->getDbAncestryIds(),
            true,
            $limit,
            $offset
        );

        return $this->mapManyEntities($programmesInSlice);
    }

    public function findAll(
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findAllWithParents(
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function findChildrenSeriesByParent(
        ProgrammeContainer $container,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findChildrenSeriesByParent(
            $container->getDbId(),
            $limit,
            $this->getOffset($limit, $page)
        );
        return $this->mapManyEntities($dbEntities);
    }

    public function countAll(string $entityType = 'Programme'): int
    {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);
        return $this->repository->countAll($entityType);
    }

    public function findByPid(Pid $pid, string $entityType = 'Programme'): ?Programme
    {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);

        $dbEntity = $this->repository->findByPid($pid, $entityType);

        return $this->mapSingleEntity($dbEntity);
    }

    public function findByPidFull(Pid $pid, string $entityType = 'Programme'): ?Programme
    {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);

        $dbEntity = $this->repository->findByPidFull($pid, $entityType);

        return $this->mapSingleEntity($dbEntity);
    }

    public function findEpisodeGuideChildren(
        Programme $programme,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findEpisodeGuideChildren(
            $programme->getDbId(),
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function countEpisodeGuideChildren(Programme $programme): int
    {
        return $this->repository->countEpisodeGuideChildren($programme->getDbId());
    }

    public function findNextSiblingByProgramme(Programme $programme): ?Programme
    {
        return $this->findSiblingByProgramme($programme, 'next');
    }

    public function findPreviousSiblingByProgramme(Programme $programme): ?Programme
    {
        return $this->findSiblingByProgramme($programme, 'previous');
    }

    public function findDescendantsByPid(
        Pid $pid,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        // in order for this to be efficient, we need to know the original programme database ID.
        // @todo - investigate another way to do this so we don't need this effectively redundant query

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

    public function countAvailableEpisodesByCategory(
        Category $category
    ): int {
        return $this->repository->countAvailableEpisodesByCategoryAncestry(
            $category->getDbAncestryIds()
        );
    }

    public function findAvailableEpisodesByCategory(
        Category $category,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findAvailableEpisodesByCategoryAncestry(
            $category->getDbAncestryIds(),
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function searchByKeywords(
        string $keywords,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {

        $dbEntities = $this->repository->findByKeywords(
            $keywords,
            false,
            $limit,
            $this->getOffset($limit, $page),
            ['Brand', 'Series', 'Episode']
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function countByKeywords(string $keywords): int
    {
        return $this->repository->countByKeywords($keywords, false, ['Brand', 'Series', 'Episode']);
    }

    public function searchAvailableByKeywords(
        string $keywords,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {

        $dbEntities = $this->repository->findByKeywords(
            $keywords,
            true,
            $limit,
            $this->getOffset($limit, $page),
            ['Brand', 'Series', 'Episode']
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function countAvailableByKeywords(string $keywords): int
    {
        return $this->repository->countByKeywords($keywords, true, ['Brand', 'Series', 'Episode']);
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
