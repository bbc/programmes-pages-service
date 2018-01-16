<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\Entity\SegmentEvent;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\SegmentEventMapper;

class SegmentEventsService extends AbstractService
{
    /* @var SegmentEventMapper */
    protected $mapper;

    /* @var SegmentEventRepository */
    protected $repository;

    public function __construct(
        SegmentEventRepository $repository,
        SegmentEventMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByPidFull(Pid $pid, $ttl = CacheInterface::NORMAL): ?SegmentEvent
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, (string) $pid, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($pid) {
                $dbEntity = $this->repository->findByPidFull($pid);

                return $this->mapSingleEntity($dbEntity);
            }
        );
    }

    /**
     * @return SegmentEvent[]
     */
    public function findLatestBroadcastedForContributor(
        Contributor $contributor,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $contributor->getDbId(), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($contributor, $limit, $page) {
                $dbEntities = $this->repository->findFullLatestBroadcastedForContributor(
                    $contributor->getDbId(),
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    /**
     * @return SegmentEvent[]
     */
    public function findByVersionWithContributions(
        Version $version,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $version->getDbId(), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($version, $limit, $page) {
                $dbEntities = $this->repository->findByVersionWithContributions(
                    [$version->getDbId()],
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    /**
     * @return SegmentEvent[]
     */
    public function findBySegmentFull(
        Segment $segment,
        bool $groupByVersionId = false,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $segment->getDbId(), $groupByVersionId, $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($segment, $groupByVersionId, $limit, $page) {
                $dbEntities = $this->repository->findBySegmentFull(
                    [$segment->getDbId()],
                    $groupByVersionId,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    /**
     * @return SegmentEvent[]
     */
    public function findBySegment(
        Segment $segment,
        bool $groupByVersionId = false,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $segment->getDbId(), $groupByVersionId, $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($segment, $groupByVersionId, $limit, $page) {
                $dbEntities = $this->repository->findBySegment(
                    [$segment->getDbId()],
                    $groupByVersionId,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }
}
