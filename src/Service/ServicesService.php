<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ServiceMapper;
use BBC\ProgrammesPagesService\Cache\CacheInterface;

class ServicesService extends AbstractService
{
    public function __construct(
        ServiceRepository $repository,
        ServiceMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByPidFull(Pid $pid): ?Service
    {
        $dbEntity = $this->repository->findByPidFull($pid);
        return $this->mapSingleEntity($dbEntity);
    }

    public function getAllInNetworks(): array
    {
        $dbEntities = $this->repository->findAllInNetworks();
        return $this->mapManyEntities($dbEntities);
    }

    public function findAllInNetwork(Nid $networkId): array
    {
        $dbEntities = $this->repository->findAllInNetwork($networkId);
        return $this->mapManyEntities($dbEntities);
    }
}
