<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Franchise;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class FranchiseTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Franchise::CLASS);
        $this->assertEquals([
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedBroadcastsCountMethodsTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = new Franchise('pid', 'title');

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\GroupProgrammeContainer',
            $entity
        );

        $this->assertEquals(0, $entity->getAggregatedBroadcastsCount());
    }
}
