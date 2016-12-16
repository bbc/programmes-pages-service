<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTimeImmutable;

class ServiceMapper extends AbstractMapper
{
    private $cache = [];

    public function getDomainModel(array $dbService): Service
    {
        $cacheKey = $dbService['id'];

        if (!array_key_exists($cacheKey, $this->cache)) {
            $this->cache[$cacheKey] = new Service(
                $dbService['id'],
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

        return $this->cache[$cacheKey];
    }

    private function getNetworkModel($dbService, $key = 'network')
    {
        if (!array_key_exists($key, $dbService) || is_null($dbService[$key])) {
            return null;
        }

        return $this->mapperFactory->getNetworkMapper()->getDomainModel($dbService[$key]);
    }
}
