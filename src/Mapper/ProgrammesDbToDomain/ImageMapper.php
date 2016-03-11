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
