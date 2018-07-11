<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PodcastRepository;
use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\Entity\Podcast;
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

    public function findByCoreEntity(
        CoreEntity $coreEntity,
        $ttl = CacheInterface::NORMAL
    ): ?Podcast {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $coreEntity->getDbId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($coreEntity) {
                $dbEntities = $this->repository->findByCoreEntityId(
                    $coreEntity->getDbId()
                );

                return $this->mapSingleEntity($dbEntities);
            }
        );
    }
}
