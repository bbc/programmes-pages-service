<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ServiceMapper;

class ServicesService extends AbstractService
{
    public function __construct(
        ServiceRepository $repository,
        ServiceMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByPidFull(Pid $pid, $ttl = CacheInterface::NORMAL): ?Service
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

    public function getAllInNetworks($ttl = CacheInterface::NORMAL): array
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () {
                $dbEntities = $this->repository->findAllInNetworks();
                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    public function findAllInNetwork(Nid $networkId, $ttl = CacheInterface::NORMAL): array
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, (string) $networkId, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($networkId) {
                $dbEntities = $this->repository->findAllInNetwork($networkId);
                return $this->mapManyEntities($dbEntities);
            }
        );
    }
}
