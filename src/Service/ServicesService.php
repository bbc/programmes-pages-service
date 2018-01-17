<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ServiceMapper;
use DateTimeImmutable;

class ServicesService extends AbstractService
{
    /* @var ServiceMapper */
    protected $mapper;

    /* @var ServiceRepository */
    protected $repository;

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

    /**
     * @return Service[]
     */
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

    /**
     * @return Service[]
     */
    public function findAllInNetworkActiveOn(Nid $networkId, DateTimeImmutable $date, $ttl = CacheInterface::NORMAL): array
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, (string) $networkId, $date->getTimestamp(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($networkId, $date) {
                $dbEntities = $this->repository->findAllInNetworkActiveOn($networkId, $date);
                return $this->mapManyEntities($dbEntities);
            }
        );
    }
}
