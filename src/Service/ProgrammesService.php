<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
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
        return $this->findSiblingByProgramme($programme, 'next');
    }

    public function findPreviousSiblingByProgramme(Programme $programme)
    {
        return $this->findSiblingByProgramme($programme, 'previous');
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

        // Then try based on ReleaseDate if we're dealing with a ProgrammeItem
        if ($programme instanceof ProgrammeItem && !is_null($programme->getReleaseDate())) {
            $dbEntity = $this->repository->findAdjacentProgrammeByReleaseDate(
                $programme->getParent()->getDbId(),
                $programme->getReleaseDate(),
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
    private function dbType(Programme $entity)
    {
        if ($entity instanceof \BBC\ProgrammesPagesService\Domain\Entity\Brand) {
            return 'Brand';
        } elseif ($entity instanceof \BBC\ProgrammesPagesService\Domain\Entity\Series) {
            return 'Series';
        } elseif ($entity instanceof \BBC\ProgrammesPagesService\Domain\Entity\Episode) {
            return 'Episode';
        } elseif ($entity instanceof \BBC\ProgrammesPagesService\Domain\Entity\Clip) {
            return 'Clip';
        }
    }
}
