<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\Entity\AtoZTitle;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;

class AtoZTitleMapper extends AbstractMapper
{
    public function getDomainModel(array $dbVersion): AtoZTitle
    {
        return new AtoZTitle(
            $dbVersion['title'],
            $dbVersion['firstLetter'],
            $this->getCoreEntityModel($dbVersion)
        );
    }

    private function getCoreEntityModel($dbVersion, $key = 'coreEntity'): Programme
    {
        if (!array_key_exists($key, $dbVersion) || is_null($dbVersion[$key])) {
            throw new DataNotFetchedException('All AtoZTitles must be joined to a CoreEntity');
        }
        return $this->mapperFactory->getProgrammeMapper()->getDomainModel($dbVersion[$key]);
    }
}
