<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedNetwork;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;

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
                new Pid($dbService['pid']),
                $dbService['name'],
                $dbService['shortName'],
                $dbService['urlKey'],
                $this->getNetworkModel($dbService),
                $this->castDateTime($dbService['startDate']),
                $this->castDateTime($dbService['endDate']),
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
