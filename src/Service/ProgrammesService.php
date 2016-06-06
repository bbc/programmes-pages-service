<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\Group;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper;

class ProgrammesService extends AbstractService
{
    public function __construct(
        CoreEntityRepository $repository,
        ProgrammeMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findAll(
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findAllWithParents(
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
     * @return int|null
     */
    public function findIdByPid(Pid $pid)
    {
        return $this->repository->findIdByPid($pid, 'Programme');
    }

    /**
     * @return Programme|null
     */
    public function findByPidFull(Pid $pid)
    {
        $dbEntity = $this->repository->findByPidFull($pid, 'Programme');

        return $this->mapSingleEntity($dbEntity);
    }

    public function findEpisodeGuideChildrenByDbId(
        int $dbId,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findEpisodeGuideChildren(
            $dbId,
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function countEpisodeGuideChildrenByDbId(int $dbId): int
    {
        return $this->repository->countEpisodeGuideChildren($dbId);
    }

    public function findNextSiblingByProgramme(Programme $programme)
    {
        $dbEntities = $this->repository->findImmediateSibling($programme, 'next');
        return $this->mapSingleEntity($dbEntities);
    }

    public function findPreviousSiblingByProgramme(Programme $programme)
    {
        $dbEntities = $this->repository->findImmediateSibling($programme, 'previous');
        return $this->mapSingleEntity($dbEntities);
    }

    public function findDescendantsByPid(
        Pid $pid,
        int $limit = self::DEFAULT_LIMIT,
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
}
