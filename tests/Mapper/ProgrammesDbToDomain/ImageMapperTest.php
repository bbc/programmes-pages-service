<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ImageMapper;

class ImageMapperTest extends BaseMapperTestCase
{
    public function testGetDomainModel()
    {
        $dbEntityArray = [
            'id' => '1',
            'pid' => 'p01m5mss',
            'title' => 'Title',
            'shortSynopsis' => 'ShortSynopsis',
            'mediumSynopsis' => 'MediumSynopsis',
            'longSynopsis' => 'LongestSynopsis',
            'type' => 'standard',
            'extension' => 'jpg',
        ];

        $pid = new Pid('p01m5mss');
        $expectedEntity = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModel($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    public function testGetDefaultImage()
    {
        $expectedEntity = new Image(
            new Pid('p01tqv8z'),
            'bbc_640x360.png',
            'BBC Blocks for /programmes',
            'BBC Blocks for /programmes',
            'standard',
            'png'
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDefaultImage());

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame($mapper->getDefaultImage(), $mapper->getDefaultImage());
    }

    private function getMapper(): ImageMapper
    {
        return new ImageMapper($this->getMapperFactory());
    }
}
