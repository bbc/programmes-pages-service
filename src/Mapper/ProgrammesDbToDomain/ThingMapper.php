<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Thing;

class ThingMapper extends AbstractMapper
{
    private $cache = [];

    public function getCacheKey(array $dbThing): string
    {
        return $this->buildCacheKey($dbThing, 'id');
    }

    public function getDomainModel(array $dbThing): Thing
    {
        $cacheKey = $this->getCacheKey($dbThing);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = new Thing(
                $dbThing['id'],
                $dbThing['preferredLabel'],
                $dbThing['disambiguationHint']
            );
        }

        return $this->cache[$cacheKey];
    }
}
