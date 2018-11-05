<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ClipRepository;
use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CoreEntityMapper;

class ClipsService extends AbstractService
{
    /* @var ClipsRepo */
    protected $repository;

    public function __construct(
        ClipRepository $repository,
        CoreEntityMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findClipsByParentPid(string $parentPid, $ttl = CacheInterface::NORMAL): array
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($parentPid) {
                $clips = $this->repository->findClipsByParentPid($parentPid);
                return $this->mapManyEntities($clips);
            }
        );
    }
}
