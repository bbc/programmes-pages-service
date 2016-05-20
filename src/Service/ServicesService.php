<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ServiceMapper;

class ServicesService extends AbstractService
{
    public function __construct(
        ServiceRepository $repository,
        ServiceMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findByPidFull(Pid $pid): ?Service
    {
        $dbEntity = $this->repository->findByPidFull($pid);

        return $this->mapSingleServiceEntity($dbEntity);
    }

    public function getAllInNetworks(): array
    {
        $dbEntities = $this->repository->findAllInNetworks();

        return $this->mapManyServiceEntities($dbEntities);
    }
}
