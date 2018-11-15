<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ClipRepository;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
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
    public function findStreamableDescendantClips(
        Programme $programme,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NORMAL
    ): array {
        return $this->findStreamableDescendantsByType($programme, 'Clip', $limit, $page, $ttl, $nullTtl);
    }

    public function countStreamableDescendantClips(
        Programme $programme,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NORMAL
    ) : int {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getPid(), 'ClipsCount', null, null, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme) {
                return $this->repository->countStreamableDescendantClips($programme->getDbAncestryIds());
            },
            [],
            $nullTtl
        );
    }

    /**
     * @return Episode[]
     */
    public function findStreamableOnDemandEpisodes(
        Programme $programme,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::SHORT
    ): array {
        return $this->findStreamableDescendantsByType($programme, 'Episode', $limit, $page, $ttl, $nullTtl, true);
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
        $nullTtl = CacheInterface::NORMAL,
        bool $useOnDemandSort = false
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getPid(), $type, $limit, $page, $ttl, $useOnDemandSort);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $type, $limit, $page, $useOnDemandSort) {
                $children = $this->repository->findStreamableDescendantsByType(
                    $programme->getDbAncestryIds(),
                    $type,
                    $limit,
                    $this->getOffset($limit, $page),
                    $useOnDemandSort
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
