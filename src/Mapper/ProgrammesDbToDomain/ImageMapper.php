<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class ImageMapper implements MapperInterface
{
    public function getDomainModel(array $dbImage): Image
    {
        return new Image(
            new Pid($dbImage['pid']),
            $dbImage['title'],
            $dbImage['shortSynopsis'],
            $this->getLongestSynopsis($dbImage),
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

    private function getLongestSynopsis($dbImage): string
    {
        if (!empty($dbImage['longSynopsis'])) {
            return $dbImage['longSynopsis'];
        }
        if (!empty($dbImage['mediumSynopsis'])) {
            return $dbImage['mediumSynopsis'];
        }
        return $dbImage['shortSynopsis'];
    }
}
