<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\SegmentEventMapper;

class SegmentEventsService extends AbstractService
{
    public function __construct(
        SegmentEventRepository $repository,
        SegmentEventMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    /**
     * @return SegmentEvent|null
     */
    public function findByPidFull(Pid $pid)
    {
        $dbEntity = $this->repository->findByPidFull($pid);

        return $this->mapSingleEntity($dbEntity);
    }

    public function findLatestBroadcastedForContributor(
        Contributor $contributor,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findFullLatestBroadcastedForContributor(
            $contributor->getDbId(),
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function findByVersion(
        Version $version,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findByVersion(
            [$version->getDbId()],
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function findBySegmentFull(
        Segment $segment,
        bool $groupByVersionId = false,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findBySegmentFull(
            [$segment->getDbId()],
            $groupByVersionId,
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function findBySegment(
        Segment $segment,
        bool $groupByVersionId = false,
        int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findBySegment(
            [$segment->getDbId()],
            $groupByVersionId,
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }
}
