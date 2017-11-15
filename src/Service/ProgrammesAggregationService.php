<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ClipRepository;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\ApplicationTime;
use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Gallery;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CoreEntityMapper;

class ProgrammesAggregationService extends AbstractService
{
    /* @var CoreEntityMapper */
    protected $mapper;

    /* @var CoreEntityRepository */
    protected $repository;

    public function __construct(
        CoreEntityRepository $repository,
        CoreEntityMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    /**
     * @return Clip[]
     */
    public function findDescendantClips(
        Programme $programme,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NORMAL
    ): array {
        return $this->findStreamableDescendantsByType($programme, 'Clip', $limit, $page, $ttl, $nullTtl);
    }

    /**
     * @return Episode[]
     */
    public function findStreamableDescendantEpisodes(
        Programme $programme,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::SHORT
    ): array {
        return $this->findStreamableDescendantsByType($programme, 'Episode', $limit, $page, $ttl, $nullTtl);
    }

    /**
     * @return Gallery[]
     */
    public function findDescendantGalleries(
        Programme $programme,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NORMAL
    ): array {
        return $this->findDescendantsByType($programme, 'Gallery', $limit, $page, $ttl, $nullTtl);
    }

    /**
     * @return ProgrammeItem[]
     */
    private function findStreamableDescendantsByType(
        Programme $programme,
        string $type,
        ?int $limit,
        int $page,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getPid(), $type, $limit, $page, $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $type, $limit, $page) {
                $children = $this->repository->findStreamableDescendantsByType(
                    $programme->getDbAncestryIds(),
                    $type,
                    ApplicationTime::getTime(),
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($children);
            },
            [],
            $nullTtl
        );
    }

    /**
     * @return CoreEntity[]
     */
    private function findDescendantsByType(
        Programme $programme,
        string $type,
        ?int $limit,
        int $page,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getPid(), $type, $limit, $page, $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $type, $limit, $page) {
                $children = $this->repository->findDescendantsByType(
                    $programme->getDbAncestryIds(),
                    $type,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($children);
            },
            [],
            $nullTtl
        );
    }
}
