<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Season;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use DateTime;

class SeasonTest extends PHPUnit_Framework_TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Season');
        $this->assertEquals([
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedBroadcastsCountTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = new Season('pid', 'title');

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\GroupProgrammeContainer',
            $entity
        );

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
