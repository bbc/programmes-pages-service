<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Entity\VersionType;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class VersionMapper extends AbstractMapper
{
    public function getDomainModel(array $dbVersion): Version
    {
        return new Version(
            new Pid($dbVersion['pid']),
            $this->getProgrammeItemModel($dbVersion),
            $dbVersion['duration'],
            $dbVersion['guidanceWarningCodes'],
            $dbVersion['competitionWarning'],
            $this->getVersionTypes($dbVersion)
        );
    }

    private function getProgrammeItemModel($dbVersion, $key = 'programmeItem'): ProgrammeItem
    {
        if (!array_key_exists($key, $dbVersion) || is_null($dbVersion[$key])) {
            throw new DataNotFetchedException('All versions must be joined to a ProgrammeItem');
        }

        return $this->mapperFactory->getProgrammeMapper()->getDomainModel($dbVersion[$key]);
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
