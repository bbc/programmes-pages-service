<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Franchise;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class FranchiseTest extends PHPUnit_Framework_TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Franchise');
        $this->assertEquals([
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedBroadcastsCountTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = new Franchise('pid', 'title');

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\GroupProgrammeContainer',
            $entity
        );
    }
}
