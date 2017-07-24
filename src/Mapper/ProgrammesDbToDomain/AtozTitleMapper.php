<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\AtozTitle;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;

class AtozTitleMapper extends AbstractMapper
{
    private $cache = [];

    public function getCacheKey(array $dbAtozTitle): string
    {
        return $this->buildCacheKey($dbAtozTitle, 'id', [
            'coreEntity' => 'CoreEntity',
        ]);
    }

    public function getDomainModel(array $dbAtozTitle): AtozTitle
    {
        $cacheKey = $this->getCacheKey($dbAtozTitle);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = new AtozTitle(
                $dbAtozTitle['title'],
                $dbAtozTitle['firstLetter'],
                $this->getCoreEntityModel($dbAtozTitle)
            );
        }

        return $this->cache[$cacheKey];
    }

    private function getCoreEntityModel(array $dbAtozTitle, string $key = 'coreEntity'): Programme
    {
        if (!isset($dbAtozTitle[$key])) {
            throw new DataNotFetchedException('All AtozTitles must be joined to a CoreEntity');
        }

        return $this->mapperFactory->getCoreEntityMapper()->getDomainModelForProgramme($dbAtozTitle[$key]);
    }
}
