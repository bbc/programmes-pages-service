<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedNetwork;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
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
                new Sid($dbService['pid']),
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
        // It is possible to have no Network, where the key does
        // exist but is set to null. We'll only say it's Unfetched
        // if the key doesn't exist at all.
        if (!array_key_exists($key, $dbService)) {
            return new UnfetchedNetwork();
        }

        if (is_null($dbService[$key])) {
            return null;
        }

        return $this->mapperFactory->getNetworkMapper()->getDomainModel($dbService[$key]);
    }
}
