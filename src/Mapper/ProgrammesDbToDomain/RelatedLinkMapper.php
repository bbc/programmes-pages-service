<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use BBC\ProgrammesPagesService\Domain\Entity\RelatedLink;

class RelatedLinkMapper implements MapperInterface
{
    public function getDomainModel(array $dbRelatedLink): RelatedLink
    {
        return new RelatedLink(
            $dbRelatedLink['title'],
            $dbRelatedLink['uri'],
            $dbRelatedLink['shortSynopsis'],
            $this->getLongestSynopsis($dbRelatedLink),
            $dbRelatedLink['type'],
            $dbRelatedLink['isExternal']
        );
    }

    private function getLongestSynopsis($dbRelatedLink): string
    {
        if (!empty($dbRelatedLink['longSynopsis'])) {
            return $dbRelatedLink['longSynopsis'];
        }
        if (!empty($dbRelatedLink['mediumSynopsis'])) {
            return $dbRelatedLink['mediumSynopsis'];
        }
        return $dbRelatedLink['shortSynopsis'];
    }
}
