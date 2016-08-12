<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class BroadcastMapper extends AbstractMapper
{
    public function getDomainModel(array $dbBroadcast): Broadcast
    {
        return new Broadcast(
            new Pid($dbBroadcast['pid']),
            $this->getService($dbBroadcast['service']),
            $dbBroadcast['startAt'],
            $dbBroadcast['endAt'],
            $dbBroadcast['duration'],
            $dbBroadcast['isBlanked'],
            $dbBroadcast['isRepeat']
        );
    }

    private function getService(array $service): Service
    {
        return $this->mapperFactory->getServiceMapper()->getDomainModel($service);
    }
}
