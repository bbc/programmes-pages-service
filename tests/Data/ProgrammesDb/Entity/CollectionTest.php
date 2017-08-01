<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Collection;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CollectionTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Collection::class);
        $this->assertEquals([
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\IsPodcastableMethodsTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = new Collection('pid', 'title');

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\GroupProgrammeContainer',
            $entity
        );
        $this->assertEquals(false, $entity->getIsPodcastable());
    }
}
