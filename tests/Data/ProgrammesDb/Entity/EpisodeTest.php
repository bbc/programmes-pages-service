<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class EpisodeTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Episode::class);
        $this->assertEquals([
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedBroadcastsCountMethodsTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableClipsCountMethodsTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableGalleriesCountMethodsTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = new Episode('pid', 'title');

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeItem',
            $entity
        );
        $this->assertEquals(0, $entity->getAggregatedBroadcastsCount());
        $this->assertEquals(0, $entity->getAvailableClipsCount());
        $this->assertEquals(0, $entity->getAvailableGalleriesCount());
    }
}
