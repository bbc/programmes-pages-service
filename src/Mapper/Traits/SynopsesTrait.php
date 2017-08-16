<?php

namespace BBC\ProgrammesPagesService\Mapper\Traits;

use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

trait SynopsesTrait
{
    /**
     * @param mixed[] $dbImage
     */
    private function getSynopses(array $dbImage): Synopses
    {
        return new Synopses(
            $dbImage['shortSynopsis'],
            $dbImage['mediumSynopsis'],
            $dbImage['longSynopsis']
        );
    }
}
