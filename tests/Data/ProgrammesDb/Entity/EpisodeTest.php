<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class EpisodeTest extends PHPUnit_Framework_TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode');
        $this->assertEquals([
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedBroadcastsCountTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableClipsCountTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableGalleriesCountTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = new Episode('pid', 'title');

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeItem',
            $entity
        );
    }
}
