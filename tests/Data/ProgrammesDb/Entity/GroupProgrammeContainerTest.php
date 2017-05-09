<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\GroupProgrammeContainer;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GroupProgrammeContainerTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(GroupProgrammeContainer::CLASS);
        $this->assertEquals([
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedEpisodesCountMethodsTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableClipsCountMethodsTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableEpisodesCountMethodsTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableGalleriesCountMethodsTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = $this->getMockForAbstractClass(
            GroupProgrammeContainer::CLASS,
            ['pid', 'title']
        );

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity',
            $entity
        );
        $this->assertEquals(0, $entity->getAggregatedEpisodesCount());
        $this->assertEquals(0, $entity->getAvailableClipsCount());
        $this->assertEquals(0, $entity->getAvailableEpisodesCount());
        $this->assertEquals(0, $entity->getAvailableGalleriesCount());
    }
}
