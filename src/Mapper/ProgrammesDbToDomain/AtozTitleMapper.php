<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\Entity\AtozTitle;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use InvalidArgumentException;

class AtozTitleMapper extends AbstractMapper
{
    private $cache = [];

    public function getDomainModel(array $dbAtozTitle): AtozTitle
    {
        $cacheKey = $dbAtozTitle['id'];

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = new AtozTitle(
                $dbAtozTitle['title'],
                $dbAtozTitle['firstLetter'],
                $this->getCoreEntityModel($dbAtozTitle)
            );
        }

        return $this->cache[$cacheKey];
    }

    private function getCoreEntityModel($dbAtozTitle, $key = 'coreEntity'): Programme
    {
        if (!isset($dbAtozTitle[$key])) {
            throw new DataNotFetchedException('All AtozTitles must be joined to a CoreEntity');
        }

        return $this->mapperFactory->getProgrammeMapper()->getDomainModel($dbAtozTitle[$key]);
    }
}
