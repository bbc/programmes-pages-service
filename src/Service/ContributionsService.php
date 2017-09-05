<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributionRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ContributionMapper;

class ContributionsService extends AbstractService
{
    /* @var ContributionMapper */
    protected $mapper;

    /* @var ContributionRepository */
    protected $repository;

    public function __construct(
        ContributionRepository $repository,
        ContributionMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    /**
     * @return Contribution[]
     */
    public function findByContributionToProgramme(
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
                $dbEntities = $this->repository->findByContributionTo(
                    [$programme->getDbId()],
                    'programme',
                    false,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    /**
     * @return Contribution[]
     */
    public function findByContributionToVersion(
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
                $dbEntities = $this->repository->findByContributionTo(
                    [$version->getDbId()],
                    'version',
                    false,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    /**
     * @return Contribution[]
     */
    public function findByContributionToSegment(
        Segment $segment,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $segment->getDbId(), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($segment, $limit, $page) {
                $dbEntities = $this->repository->findByContributionTo(
                    [$segment->getDbId()],
                    'segment',
                    false,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    /**
     * @return Contribution[]
     */
    public function findByContributionToSegments(
        array $segments,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $segmentIds = array_map(function (Segment $s) {
            return $s->getDbId();
        }, $segments);
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, implode('|', $segmentIds), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($segmentIds, $limit, $page) {
                $dbEntities = $this->repository->findByContributionTo(
                    $segmentIds,
                    'segment',
                    true,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }
}
