<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\RelatedLink;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class RelatedLinkMapper extends AbstractMapper
{
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
                $dbRelatedLink['type'],
                $dbRelatedLink['isExternal']
            );
        }

        return $this->cache[$cacheKey];
    }

    private function getSynopses($dbRelatedLink): Synopses
    {
        return new Synopses(
            $dbRelatedLink['shortSynopsis'],
            $dbRelatedLink['mediumSynopsis'],
            $dbRelatedLink['longSynopsis']
        );
    }
}
