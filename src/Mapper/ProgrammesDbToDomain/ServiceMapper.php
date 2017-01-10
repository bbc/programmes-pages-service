<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTimeImmutable;

class ServiceMapper extends AbstractMapper
{
    private $cache = [];

    public function getCacheKey(array $dbService): string
    {
        return $this->buildCacheKey($dbService, 'id', [
            'network' => 'Network',
        ]);
    }

    public function getDomainModel(array $dbService): Service
    {
        $cacheKey = $this->getCacheKey($dbService);

        if (!isset($this->cache[$cacheKey])) {
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

    private function getNetworkModel(array $dbService, string $key = 'network'): ?Network
    {
        if (!isset($dbService[$key])) {
            return null;
        }

        return $this->mapperFactory->getNetworkMapper()->getDomainModel($dbService[$key]);
    }
}
