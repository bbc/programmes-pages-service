<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class ProgrammeContainerTest extends PHPUnit_Framework_TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeContainer');
        $this->assertEquals([
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedBroadcastsCountTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedEpisodesCountTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableClipsCountTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableEpisodesCountTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableGalleriesCountTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\IsPodcastableTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = $this->getMockForAbstractClass(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeContainer',
            ['pid', 'title']
        );

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Programme',
            $entity
        );

        $this->assertSame(null, $entity->getExpectedChildCount());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = $this->getMockForAbstractClass(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeContainer',
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
