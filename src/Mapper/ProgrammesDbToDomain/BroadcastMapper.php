<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedService;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use DateTimeImmutable;

class BroadcastMapper extends AbstractMapper
{
    private $cache = [];

    public function getCacheKey(array $dbBroadcast): string
    {
        return $this->buildCacheKey($dbBroadcast, 'id', [
            'programmeItem' => 'Programme',
            'version' => 'Version',
            'service' => 'Service',
        ]);
    }

    public function getDomainModel(array $dbBroadcast): Broadcast
    {
        $cacheKey = $this->getCacheKey($dbBroadcast);

        if (!isset($this->cache[$cacheKey])) {
            if ($dbBroadcast['isWebcast']) {
                $this->cache[$cacheKey] = $this->getWebcastDomainModel($dbBroadcast);
            } else {
                $this->cache[$cacheKey] = $this->getBroadcastDomainModel($dbBroadcast);
            }
        }

        return $this->cache[$cacheKey];
    }

    private function getBroadcastDomainModel(array $dbBroadcast): Broadcast
    {
        return new Broadcast(
            new Pid($dbBroadcast['pid']),
            $this->getVersionModel($dbBroadcast),
            $this->getProgrammeItemModel($dbBroadcast),
            $this->getServiceModel($dbBroadcast),
            DateTimeImmutable::createFromMutable($dbBroadcast['startAt']),
            DateTimeImmutable::createFromMutable($dbBroadcast['endAt']),
            $dbBroadcast['duration'],
            $dbBroadcast['isBlanked'],
            $dbBroadcast['isRepeat']
        );
    }

    private function getWebcastDomainModel(array $dbWebcast)
    {
       // TODO Webcast domain objects have not yet been implemented
        return null;
    }

    private function getVersionModel(array $dbBroadcast, string $key = 'version'): Version
    {
        // It is not valid for a Broadcast to have no version
        // so it counts as Unfetched even if the key exists but is null
        if (!isset($dbBroadcast[$key])) {
            return new UnfetchedVersion();
        }

        return $this->mapperFactory->getVersionMapper()->getDomainModel($dbBroadcast[$key]);
    }

    private function getProgrammeItemModel(array $dbBroadcast, string $key = 'programmeItem'): ProgrammeItem
    {
        // Inverted logic compared to other model getters as we have two choices
        // of where to get the ProgrammeItem from - either directly attached to
        // the Broadcast or via the Version.

        // Prefer the ProgrammeItem that comes via the Version, as that is a
        // canonical relationship, while the ProgrammeItem attached to the
        // Broadcaast is a Denorm.
        // It is not valid for a Version to have no programmeItem
        // so it counts as Unfetched even if the key exists but is null
        if (isset($dbBroadcast['version'][$key])) {
            return $this->mapperFactory->getProgrammeMapper()->getDomainModel($dbBroadcast['version'][$key]);
        }

        // It is not valid for a Broadcast to have no programmeItem
        // so it counts as Unfetched even if the key exists but is null
        if (isset($dbBroadcast[$key])) {
            return $this->mapperFactory->getProgrammeMapper()->getDomainModel($dbBroadcast[$key]);
        }

        return new UnfetchedProgrammeItem();
    }

    private function getServiceModel(array $dbBroadcast, string $key = 'service'): Service
    {
        // It is not valid for a Broadcast to have no service
        // so it counts as Unfetched even if the key exists but is null
        if (!isset($dbBroadcast[$key])) {
            return new UnfetchedService();
        }

        return $this->mapperFactory->getServiceMapper()->getDomainModel($dbBroadcast[$key]);
    }
}
