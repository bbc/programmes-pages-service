<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeContainer;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper;
use InvalidArgumentException;

class ProgrammesService extends AbstractService
{
    const ALL_VALID_ENTITY_TYPES = [
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
        ProgrammeMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findAll(
        $limit = self::DEFAULT_LIMIT,
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
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findChildrenSeriesByParent(
            $container->getDbId(),
            $limit,
            $this->getOffset($limit, $page)
        );
        return $this->mapManyEntities($dbEntities);
    }

    public function countAll(): int
    {
        return $this->repository->countAll();
    }

    /**
     * @param Pid $pid
     * @param string $entityType
     * @return Programme|null
     */
    public function findByPid(Pid $pid, string $entityType = 'Programme')
    {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);

        $dbEntity = $this->repository->findByPid($pid, $entityType);

        return $this->mapSingleEntity($dbEntity);
    }

    /**
     * @param Pid $pid
     * @return Programme|null
     */
    public function findByPidFull(Pid $pid, string $entityType = 'Programme')
    {
        $this->assertEntityType($entityType, self::ALL_VALID_ENTITY_TYPES);

        $dbEntity = $this->repository->findByPidFull($pid, $entityType);

        return $this->mapSingleEntity($dbEntity);
    }

    public function findEpisodeGuideChildren(
        Programme $programme,
        $limit = self::DEFAULT_LIMIT,
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

    public function findNextSiblingByProgramme(Programme $programme)
    {
        return $this->findSiblingByProgramme($programme, 'next');
    }

    public function findPreviousSiblingByProgramme(Programme $programme)
    {
        return $this->findSiblingByProgramme($programme, 'previous');
    }

    public function findDescendantsByPid(
        Pid $pid,
        $limit = self::DEFAULT_LIMIT,
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

    public function findFormatAvailableEpisodesByCategory(
        string $category1,
        string $category2,
        string $category3,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    )
    {
        $dbEntities = $this->repository->findAvailableEpisodesByUrlKeyAndType(
            'format',
            $category1,
            $category2,
            $category3,
            $limit,
            $this->getOffset($limit, $page)
        );
    }

    public function findGenreAvailableEpisodesByCategory(
        string $category1,
        string $category2,
        string $category3,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    )
    {
        $dbEntities = $this->repository->findAvailableEpisodesByUrlKeyAndType(
            'genre',
            $category1,
            $category2,
            $category3,
            $limit,
            $this->getOffset($limit, $page)
        );
    }

    public function findProgrammesByKeywords(
        string $keywords,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {

        $dbEntities = $this->repository->findByKeywords(
            $keywords,
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function countProgrammesByKeywords(string $keywords): int
    {
        return $this->repository->countByKeywords($keywords);
    }

    private function findSiblingByProgramme(Programme $programme, string $direction)
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
     *
     * @param Programme $entity
     * @return null|string
     */
    private function dbType(Programme $entity)
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
