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
    ): array {
        $dbEntities = $this->programmeRepository->findAllWithParents(
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyProgrammeEntities($dbEntities);
    }

    public function countAll(): int
    {
        return $this->programmeRepository->countAll();
    }

    /**
     * @return CoreEntity|null
     */
    public function findByPidFull(Pid $pid)
    {
        $dbEntity = $this->programmeRepository->findByPidFull($pid);

        return $this->mapSingleProgrammeEntity($dbEntity);
    }

    public function findEpisodeGuideChildrenByPid(
        Pid $pid,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $parent = $this->programmeRepository->findByPidFull((string) $pid);

        if (is_null($parent)) {
            return [];
        }

        $dbEntities = $this->programmeRepository->findEpisodeGuideChildren(
            $parent['id'],
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyProgrammeEntities($dbEntities);
    }

    public function countEpisodeGuideChildrenByPid(Pid $pid): int
    {
        $parent = $this->programmeRepository->findByPidFull((string) $pid);

        if (is_null($parent)) {
            return 0;
        }

        return $this->programmeRepository->countEpisodeGuideChildren($parent['id']);
    }

    public function findDescendantsByPid(
        Pid $pid,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
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

        return $this->mapManyProgrammeEntities($dbEntities);
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
