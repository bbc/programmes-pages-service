<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class ImageMapper implements MapperInterface
{
    public function getDomainModel(array $dbImage): Image
    {
        return new Image(
            new Pid($dbImage['pid']),
            $dbImage['title'],
            $dbImage['shortSynopsis'],
            $this->getSynopses($dbImage)->getLongestSynopsis(),
            $dbImage['type'],
            $dbImage['extension']
        );
    }

    public function getDefaultImage(): Image
    {
        return new Image(
            new Pid('p01tqv8z'),
            'bbc_640x360.png',
            'BBC Blocks for /programmes',
            'BBC Blocks for /programmes',
            'standard',
            'png'
        );
    }

    private function getSynopses($dbImage): Synopses
    {
        return new Synopses(
            $dbImage['shortSynopsis'],
            $dbImage['mediumSynopsis'],
            $dbImage['longSynopsis']
        );
    }
}
