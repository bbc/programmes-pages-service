<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use BBC\ProgrammesPagesService\Domain\Entity\RelatedLink;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class RelatedLinkMapper implements MapperInterface
{
    public function getDomainModel(array $dbRelatedLink): RelatedLink
    {
        return new RelatedLink(
            $dbRelatedLink['title'],
            $dbRelatedLink['uri'],
            $dbRelatedLink['shortSynopsis'],
            $this->getSynopses($dbRelatedLink)->getLongestSynopsis(),
            $dbRelatedLink['type'],
            $dbRelatedLink['isExternal']
        );
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
