<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Entity\VersionType;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class VersionMapper extends AbstractMapper
{
    public function getDomainModel(array $dbVersion): Version
    {
        return new Version(
            new Pid($dbVersion['pid']),
            $dbVersion['duration'],
            $dbVersion['guidanceWarningCodes'],
            $dbVersion['competitionWarning'],
            $this->getProgrammeItemModel($dbVersion),
            $this->getVersionTypes($dbVersion)
        );
    }

    private function getProgrammeItemModel($dbVersion, $key = 'programmeItem')
    {
        if (!array_key_exists($key, $dbVersion) || is_null($dbVersion[$key])) {
            return null;
        }

        return $this->mapperProvider->getProgrammeMapper()->getDomainModel($dbVersion[$key]);
    }

    private function getVersionTypes($dbVersion, $key = 'versionTypes'): array
    {
        if (!array_key_exists($key, $dbVersion) || is_null($dbVersion[$key])) {
            return [];
        }

        return array_map([$this, 'getVersionTypeModel'], $dbVersion[$key]);
    }

    private function getVersionTypeModel(array $dbVersionType): VersionType
    {
        return new VersionType(
            $dbVersionType['type'],
            $dbVersionType['name']
        );
    }
}
