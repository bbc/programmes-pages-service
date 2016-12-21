<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\SegmentEvent;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class SegmentEventTest extends PHPUnit_Framework_TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(SegmentEvent::CLASS);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\SynopsesTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $mockVersion = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version');
        $mockSegment = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Segment');

        $entity = new SegmentEvent('pid', $mockVersion, $mockSegment);

        $this->assertSame(null, $entity->getId());
        $this->assertSame('pid', $entity->getPid());
        $this->assertSame($mockVersion, $entity->getVersion());
        $this->assertSame($mockSegment, $entity->getSegment());
        $this->assertSame(null, $entity->getOffset());
        $this->assertSame(null, $entity->getPosition());
        $this->assertSame(null, $entity->getTitle());
        $this->assertSame(false, $entity->getIsChapter());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $mockVersion = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version');
        $mockSegment = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Segment');

        $entity = new SegmentEvent('pid', $mockVersion, $mockSegment);

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        $mockVersion = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version');
        $mockSegment = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Segment');

        return [
            ['Pid', 'newPid'],
            ['Version', $mockVersion],
            ['Segment', $mockSegment],
            ['Title', 'newTitle'],
            ['Offset', 1],
            ['Position', 1],
            ['IsChapter', true],
        ];
    }
}
