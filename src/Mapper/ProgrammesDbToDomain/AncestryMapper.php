<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Ancestry;

class AncestryMapper extends AbstractMapper
{

    private $cache = [];

    private $cachedDefaultImage;

    public function getCacheKey(array $dbImage): string
    {
        return $this->buildCacheKey($dbImage, 'id');
    }

    public function getDomainModel(array $dbAncestry): Ancestry
    {
        $cacheKey = $this->getCacheKey($dbAncestry);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = new Ancestry(
                $dbAncestry['coreEntityId'],
                $dbAncestry['ancestorId'],
            );
        }

        return $this->cache[$cacheKey];
    }
}
