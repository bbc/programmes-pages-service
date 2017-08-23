<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper;

class VersionsService extends AbstractService
{
    /* @var VersionMapper */
    protected $mapper;

    /* @var VersionRepository */
    protected $repository;

    public function __construct(
        VersionRepository $repository,
        VersionMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByPidFull(Pid $pid, $ttl = CacheInterface::NORMAL): ?Version
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

    public function findByProgrammeItem(ProgrammeItem $programmeItem, $ttl = CacheInterface::NORMAL): array
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programmeItem->getDbId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programmeItem) {
                $dbEntities = $this->repository->findByProgrammeItem($programmeItem->getDbId());

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    public function findOriginalVersionForProgrammeItem(ProgrammeItem $programmeItem, $ttl = CacheInterface::NORMAL): ?Version
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programmeItem->getDbId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programmeItem) {
                $dbEntity = $this->repository->findOriginalVersionForProgrammeItem($programmeItem->getDbId());

                return $this->mapSingleEntity($dbEntity);
            }
        );
    }
}
