<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use BBC\ProgrammesPagesService\Domain\Entity\RelatedLink;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class RelatedLinkMapper implements MapperInterface
{
    private $cache = [];

    public function getDomainModel(array $dbRelatedLink): RelatedLink
    {
        $cacheKey = $dbRelatedLink['id'];

        if (!array_key_exists($cacheKey, $this->cache)) {
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
