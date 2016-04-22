<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTimeImmutable;

class ServiceMapper extends AbstractMapper
{
    public function getDomainModel(array $dbService): Service
    {
        return new Service(
            new Sid($dbService['sid']),
            $dbService['name'],
            $dbService['shortName'],
            $dbService['urlKey'],
            $this->getNetworkModel($dbService),
            ($dbService['startDate'] ? DateTimeImmutable::createFromMutable($dbService['startDate']) : null),
            ($dbService['endDate'] ? DateTimeImmutable::createFromMutable($dbService['endDate']) : null),
            $dbService['liveStreamUrl']
        );
    }

    private function getNetworkModel($dbService, $key = 'network')
    {
        if (!array_key_exists($key, $dbService) || is_null($dbService[$key])) {
            return null;
        }

        return $this->mapperProvider->getNetworkMapper()->getDomainModel($dbService[$key]);
    }
}
