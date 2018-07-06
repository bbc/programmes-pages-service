<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PodcastRepository;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\PodcastMapper;

class PodcastsService extends AbstractService
{
    /* @var PodcastMapper */
    protected $mapper;

    /* @var PodcastRepository */
    protected $repository;

    public function __construct(
        PodcastRepository $repository,
        PodcastMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByCoreEntityId(
        int $coreEntityId,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $coreEntityId, $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($coreEntityId, $limit, $page) {
                $dbEntities = $this->repository->findByCoreEntityId(
                    $coreEntityId,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }
}
