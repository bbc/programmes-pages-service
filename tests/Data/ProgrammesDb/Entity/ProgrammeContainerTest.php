<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeContainer;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ProgrammeContainerTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(ProgrammeContainer::class);
        $this->assertEquals([
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedBroadcastsCountMethodsTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedEpisodesCountMethodsTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableClipsCountMethodsTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableEpisodesCountMethodsTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\IsPodcastableMethodsTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = $this->getMockForAbstractClass(
            ProgrammeContainer::class,
            ['pid', 'title']
        );

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Programme',
            $entity
        );

        $this->assertSame(null, $entity->getExpectedChildCount());
        // Test defaults on properties pulled down from traits
        $this->assertEquals(0, $entity->getAggregatedBroadcastsCount());
        $this->assertEquals(0, $entity->getAvailableEpisodesCount());
        $this->assertEquals(0, $entity->getAvailableClipsCount());
        $this->assertEquals(0, $entity->getAggregatedEpisodesCount());
        $this->assertEquals(false, $entity->getIsPodcastable());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = $this->getMockForAbstractClass(
            ProgrammeContainer::class,
            ['pid', 'title']
        );

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['ExpectedChildCount', 1],
        ];
    }
}
