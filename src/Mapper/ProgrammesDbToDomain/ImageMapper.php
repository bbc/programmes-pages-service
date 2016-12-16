<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class ImageMapper implements MapperInterface
{
    private $cache = [];

    private $cachedDefaultImage;

    public function getDomainModel(array $dbImage): Image
    {
        $cacheKey = $dbImage['id'];

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = new Image(
                new Pid($dbImage['pid']),
                $dbImage['title'],
                $dbImage['shortSynopsis'],
                $this->getSynopses($dbImage)->getLongestSynopsis(),
                $dbImage['type'],
                $dbImage['extension']
            );
        }

        return $this->cache[$cacheKey];
    }

    public function getDefaultImage(): Image
    {
        if (!$this->cachedDefaultImage) {
            $this->cachedDefaultImage = new Image(
                new Pid('p01tqv8z'),
                'bbc_640x360.png',
                'BBC Blocks for /programmes',
                'BBC Blocks for /programmes',
                'standard',
                'png'
            );
        }

        return $this->cachedDefaultImage;
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
