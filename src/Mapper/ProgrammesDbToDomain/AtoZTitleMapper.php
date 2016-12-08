<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\Entity\AtoZTitle;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use InvalidArgumentException;

class AtoZTitleMapper extends AbstractMapper
{
    public function getDomainModel(array $dbAtoZTitle): AtoZTitle
    {
        return new AtoZTitle(
            $dbAtoZTitle['title'],
            $dbAtoZTitle['firstLetter'],
            $this->getCoreEntityModel($dbAtoZTitle)
        );
    }

    private function getCoreEntityModel($dbAtoZTitle, $key = 'coreEntity'): Programme
    {
        if (!array_key_exists($key, $dbAtoZTitle) || is_null($dbAtoZTitle[$key])) {
            throw new DataNotFetchedException('All AtoZTitles must be joined to a CoreEntity');
        }

        return $this->mapperFactory->getProgrammeMapper()->getDomainModel($dbAtoZTitle[$key]);
    }
}
