<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\RelatedLink;
use BBC\ProgrammesPagesService\Mapper\Traits\SynopsesTrait;

class RelatedLinkMapper extends AbstractMapper
{
    use SynopsesTrait;

    private $cache = [];

    public function getCacheKey(array $dbRelatedLink): string
    {
        return $this->buildCacheKey($dbRelatedLink, 'id');
    }

    public function getDomainModel(array $dbRelatedLink): RelatedLink
    {
        $cacheKey = $this->getCacheKey($dbRelatedLink);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = new RelatedLink(
                $dbRelatedLink['title'],
                $dbRelatedLink['uri'],
                $dbRelatedLink['shortSynopsis'],
                $this->getSynopses($dbRelatedLink)->getLongestSynopsis(),
                $dbRelatedLink['type']
            );
        }

        return $this->cache[$cacheKey];
    }
}
