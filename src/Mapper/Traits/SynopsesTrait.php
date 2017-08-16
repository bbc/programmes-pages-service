<?php

namespace BBC\ProgrammesPagesService\Mapper\Traits;

use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

trait SynopsesTrait
{
    /**
     * @param mixed[] $entity
     */
    private function getSynopses(array $entity): Synopses
    {
        return new Synopses(
            $entity['shortSynopsis'],
            $entity['mediumSynopsis'],
            $entity['longSynopsis']
        );
    }
}
