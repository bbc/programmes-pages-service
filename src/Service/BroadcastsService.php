<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\BroadcastMapper;

class BroadcastsService extends AbstractService
{
    public function __construct(
        BroadcastRepository $repository,
        BroadcastMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findBroadcastsOfVersion(Version $version)
    {
        $broadcasts = $this->repository->findBroadcastsOfVersionId($version->getDbId());
        $mappedBroadcasts = [];
        foreach($broadcasts as $broadcast){
            $mappedBroadcasts[] = $this->mapper->getDomainModel($broadcast);
        }
        return $mappedBroadcasts;
    }
}
