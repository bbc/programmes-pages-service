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
            $dbImage['longestSynopsis'],
            $dbImage['type'],
            $dbImage['extension']
        );
    }
}
