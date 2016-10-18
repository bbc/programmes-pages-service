<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use DateTimeImmutable;

class CollapsedBroadcastMapper extends AbstractMapper
{
    /**
     * @param array $dbCollapsedBroadcast
     * @param array $services
     *
     * @return CollapsedBroadcast|null
     */
    public function getDomainModel(array $dbCollapsedBroadcast, array $services = []): CollapsedBroadcast
    {
        return new CollapsedBroadcast(
            $this->getVersionModel($dbCollapsedBroadcast),
            $this->getProgrammeItemModel($dbCollapsedBroadcast[0]['version']),
            $this->getServiceModels($dbCollapsedBroadcast['serviceIds'], $services),
            DateTimeImmutable::createFromMutable($dbCollapsedBroadcast[0]['startAt']),
            DateTimeImmutable::createFromMutable($dbCollapsedBroadcast[0]['endAt']),
            $dbCollapsedBroadcast[0]['duration'],
            $dbCollapsedBroadcast[0]['isBlanked'],
            $dbCollapsedBroadcast[0]['isRepeat']
        );
    }

    private function getVersionModel($dbCollapsedBroadcast, $key = 'version'): Version
    {
        // It is not valid for a CollapsedBroadcast to have no version
        // so it counts as Unfetched even if the key exists but is null
        if (!array_key_exists($key, $dbCollapsedBroadcast) || is_null($dbCollapsedBroadcast[$key])) {
            return new UnfetchedVersion();
        }

        return $this->mapperFactory->getVersionMapper()->getDomainModel($dbCollapsedBroadcast[$key]);
    }

    private function getProgrammeItemModel($dbCollapsedBroadcast, $key = 'programmeItem'): Programme
    {
        // Inverted logic compared to other model getters as we have two choices
        // of where to get the ProgrammeItem from - either directly attached to
        // the CollapsedBroadcast or via the Version.

        // Prefer the ProgrammeItem that comes via the Version, as that is a
        // canonical relationship, while the ProgrammeItem attached to the
        // CollapsedBroadcast is a Denorm.
        // It is not valid for a Version to have no programmeItem
        // so it counts as Unfetched even if the key exists but is null
        $hasVersion = array_key_exists('version', $dbCollapsedBroadcast);
        if ($hasVersion && array_key_exists(
            $key,
            $dbCollapsedBroadcast['version']
        ) && !is_null($dbCollapsedBroadcast['version'][$key])) {
            return $this->mapperFactory->getProgrammeMapper()->getDomainModel($dbCollapsedBroadcast['version'][$key]);
        }

        // It is not valid for a CollapsedBroadcast to have no programmeItem
        // so it counts as Unfetched even if the key exists but is null
        if (array_key_exists($key, $dbCollapsedBroadcast) && !is_null($dbCollapsedBroadcast[$key])) {
            return $this->mapperFactory->getProgrammeMapper()->getDomainModel($dbCollapsedBroadcast[$key]);
        }

        return new UnfetchedProgrammeItem();
    }

    private function getServiceModels(array $serviceIds, array $services): array
    {
        $serviceModels = [];
        foreach ($serviceIds as $sid) {
            if (array_key_exists($sid, $services)) {
                $serviceModels[] = $this->mapperFactory->getServiceMapper()->getDomainModel($services[$sid]);
            }
        }

        return $serviceModels;
    }
}
