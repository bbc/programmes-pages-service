<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper;

class ProgrammesService
{
    const DEFAULT_PAGE = 1;
    const DEFAULT_LIMIT = 50;

    protected $programmeRepository;

    protected $programmeMapper;

    public function __construct(
        CoreEntityRepository $programmeRepository,
        ProgrammeMapper $programmeMapper
    ) {
        $this->programmeRepository = $programmeRepository;
        $this->programmeMapper = $programmeMapper;
    }

    public function findAll(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): EntityCollectionServiceResult {
        $dbEntities = $this->programmeRepository->findAllWithParents(
            $limit,
            $this->getOffset($limit, $page)
        );

        return new EntityCollectionServiceResult(
            $this->mapManyProgrammeEntities($dbEntities),
            $limit,
            $page
        );
    }

    public function countAll(): int
    {
        return $this->programmeRepository->countAll();
    }

    public function findByPidFull(
        Pid $pid
    ): EntitySingleServiceResult {
        $dbEntity = $this->programmeRepository->findByPidFull($pid);

        return new EntitySingleServiceResult(
            $this->mapSingleProgrammeEntity($dbEntity)
        );
    }

    public function findChildrenByPid(
        Pid $pid,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): EntityCollectionServiceResult {
        $parent = $this->programmeRepository->findByPidFull((string) $pid);

        $dbEntities = $this->programmeRepository->findChildren(
            $parent['id'],
            $limit,
            $this->getOffset($limit, $page)
        );

        return new EntityCollectionServiceResult(
            $this->mapManyProgrammeEntities($dbEntities),
            $limit,
            $page
        );
    }

    public function countChildrenByPid(Pid $pid): int
    {
        $parent = $this->programmeRepository->findByPidFull((string) $pid);

        return $this->programmeRepository->countChildren($parent['id']);
    }

    public function findDescendantsByPid(
        Pid $pid,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): EntityCollectionServiceResult {
        // in order for this to be efficient, we need to know the original programme database ID.
        // @todo - investigate another way to do this so we don't need this effectively redundant query

        $dbEntity = $this->programmeRepository->findByPidFull($pid);
        if (!$dbEntity) {
            return null;
        }

        $dbEntities = $this->programmeRepository->findDescendants(
            $dbEntity,
            $limit,
            $this->getOffset($limit, $page)
        );

        return new EntityCollectionServiceResult(
            $this->mapManyProgrammeEntities($dbEntities),
            $limit,
            $page
        );
    }

    protected function getOffset($limit, $page): int
    {
        return $limit * ($page - 1);
    }

    protected function mapSingleProgrammeEntity($dbEntity)
    {
        if (is_null($dbEntity)) {
            return null;
        }

        return $this->programmeMapper->getDomainModel($dbEntity);
    }

    protected function mapManyProgrammeEntities(array $dbEntities): array
    {
        return array_map([$this, 'mapSingleProgrammeEntity'], $dbEntities);
    }
}
