<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use DateTimeImmutable;

class VersionMapper extends AbstractMapper
{
    private $cache = [];

    public function getCacheKey(array $dbVersion): string
    {
        return $this->buildCacheKey($dbVersion, 'id', [
            'programmeItem' => 'Programme',
        ], [
            'versionTypes' => 'VersionType',
        ]);
    }

    public function getDomainModel(array $dbVersion): Version
    {
        $cacheKey = $this->getCacheKey($dbVersion);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = new Version(
                $dbVersion['id'],
                new Pid($dbVersion['pid']),
                $this->getProgrammeItemModel($dbVersion),
                $dbVersion['streamable'],
                $dbVersion['downloadable'],
                $dbVersion['segmentEventCount'],
                $dbVersion['contributionsCount'],
                $dbVersion['duration'],
                $dbVersion['guidanceWarningCodes'],
                $dbVersion['competitionWarning'],
                ($dbVersion['streamableFrom'] ? DateTimeImmutable::createFromMutable($dbVersion['streamableFrom']) : null),
                ($dbVersion['streamableUntil'] ? DateTimeImmutable::createFromMutable($dbVersion['streamableUntil']) : null),
                $this->getVersionTypes($dbVersion)
            );
        }

        return $this->cache[$cacheKey];
    }

    private function getProgrammeItemModel($dbVersion, $key = 'programmeItem'): ProgrammeItem
    {
        if (!isset($dbVersion[$key])) {
            throw new DataNotFetchedException('All versions must be joined to a ProgrammeItem');
        }

        return $this->mapperFactory->getProgrammeMapper()->getDomainModel($dbVersion[$key]);
    }

    private function getVersionTypes($dbVersion, $key = 'versionTypes')
    {
        if (!isset($dbVersion[$key]) || !is_array($dbVersion[$key])) {
            return null;
        }

        $versionTypeMapper = $this->mapperFactory->getVersionTypeMapper();
        $versionTypes = [];
        foreach ($dbVersion[$key] as $dbVersionType) {
            $versionTypes[] = $versionTypeMapper->getDomainModel($dbVersionType);
        }

        return $versionTypes;
    }
}
