<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ThingRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Thing;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ThingMapper;

class ThingsService extends AbstractService
{
    /** @var ThingMapper */
    protected $mapper;

    /** @var ThingRepository */
    protected $repository;

    public function __construct(
        ThingRepository $repository,
        ThingMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findById(string $id, $ttl = CacheInterface::NORMAL): ?Thing
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $id, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($id) {
                return $this->mapSingleEntity(
                    $this->repository->findById($id)
                );
            }
        );
    }
}
