<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ClipRepository;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeContainer;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CoreEntityMapper;
use BBC\ProgrammesPagesService\Cache\CacheInterface;

class ProgrammesAggregationService extends AbstractService
{
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
        int $page = self::DEFAULT_PAGE
    ): array {
        return $this->findStreamableDescendantsByType($programme, 'Clip', $limit, $page);
    }

    /**
     * @return Gallery[]
     */
    public function findDescendantGalleries(
        Programme $programme,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        return $this->findDescendantsByType($programme, 'Gallery', $limit, $page);
    }

    /**
     * @return ProgrammeItem[]
     */
    private function findStreamableDescendantsByType(
        Programme $programme,
        string $type,
        ?int $limit,
        int $page,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $type, $limit, $page, $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $type, $limit, $page) {
                $children = $this->repository->findStreamableDescendantsByType(
                    $programme->getDbAncestryIds(),
                    $type,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($children);
            }
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
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $type, $limit, $page, $ttl);
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
            }
        );
    }
}
