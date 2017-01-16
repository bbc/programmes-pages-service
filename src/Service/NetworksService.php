<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\NetworkRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\NetworkMapper;

class NetworksService extends AbstractService
{
    public function __construct(
        NetworkRepository $repository,
        NetworkMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findByUrlKeyWithDefaultService(string $urlKey): ?Network
    {
        $dbEntity = $this->repository->findByUrlKeyWithDefaultService($urlKey);
        return $this->mapSingleEntity($dbEntity);
    }
}
