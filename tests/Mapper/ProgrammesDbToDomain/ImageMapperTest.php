<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ImageMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use PHPUnit_Framework_TestCase;

class ImageMapperTest extends PHPUnit_Framework_TestCase
{
    public function testGetDomainModel()
    {
        $dbEntityArray = [
            'id' => '1',
            'pid' => 'p01m5mss',
            'title' => 'Title',
            'shortSynopsis' => 'ShortSynopsis',
            'type' => 'standard',
            'extension' => 'jpg',
        ];

        $pid = new Pid('p01m5mss');
        $expectedEntity = new Image($pid, 'Title', 'ShortSynopsis', 'standard', 'jpg');

        $mapper = new ImageMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));
    }
}
