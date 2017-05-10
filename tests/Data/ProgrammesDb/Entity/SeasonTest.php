<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Season;
use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class SeasonTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Season::CLASS);
        $this->assertEquals([
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedBroadcastsCountMethodsTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = new Season('pid', 'title');

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\GroupProgrammeContainer',
            $entity
        );

        $this->assertEquals(0, $entity->getAggregatedBroadcastsCount());
        $this->assertSame(null, $entity->getStartDate());
        $this->assertSame(null, $entity->getEndDate());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new Season('pid', 'title');

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['startDate', new DateTime()],
            ['endDate', new DateTime()],
        ];
    }
}
