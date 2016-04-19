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

    protected $coreEntityRepository;

    protected $programmeMapper;

    public function __construct(
        CoreEntityRepository $coreEntityRepository,
        ProgrammeMapper $programmeMapper
    ) {
        $this->coreEntityRepository = $coreEntityRepository;
        $this->programmeMapper = $programmeMapper;
    }

    public function findAll(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->coreEntityRepository->findAllWithParents(
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyProgrammeEntities($dbEntities);
    }

    public function countAll(): int
    {
        return $this->coreEntityRepository->countAll();
    }

    /**
     * @return CoreEntity|null
     */
    public function findByPidFull(Pid $pid)
    {
        $dbEntity = $this->coreEntityRepository->findByPidFull($pid);

        return $this->mapSingleProgrammeEntity($dbEntity);
    }

    public function findEpisodeGuideChildrenByPid(
        Pid $pid,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $parent = $this->coreEntityRepository->findByPidFull((string) $pid);

        if (is_null($parent)) {
            return [];
        }

        $dbEntities = $this->coreEntityRepository->findEpisodeGuideChildren(
            $parent['id'],
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyProgrammeEntities($dbEntities);
    }

    public function countEpisodeGuideChildrenByPid(Pid $pid): int
    {
        $parent = $this->coreEntityRepository->findByPidFull((string) $pid);

        if (is_null($parent)) {
            return 0;
        }

        return $this->coreEntityRepository->countEpisodeGuideChildren($parent['id']);
    }

    public function findDescendantsByPid(
        Pid $pid,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        // in order for this to be efficient, we need to know the original programme database ID.
        // @todo - investigate another way to do this so we don't need this effectively redundant query

        $dbEntity = $this->coreEntityRepository->findByPidFull($pid);
        if (!$dbEntity) {
            return null;
        }

        $dbEntities = $this->coreEntityRepository->findDescendants(
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
