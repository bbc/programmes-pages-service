<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\VersionType;

class VersionTypeMapper extends AbstractMapper
{
    private $cache = [];

    public function getCacheKey(array $dbVersionType): string
    {
        return $this->buildCacheKey($dbVersionType, 'id');
    }

    public function getDomainModel(array $dbVersionType): VersionType
    {
        $cacheKey = $this->getCacheKey($dbVersionType);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = new VersionType(
                $dbVersionType['type'],
                $dbVersionType['name']
            );
        }

        return $this->cache[$cacheKey];
    }
}
