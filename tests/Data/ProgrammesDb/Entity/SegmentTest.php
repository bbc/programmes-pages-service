<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Segment;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class SegmentTest extends PHPUnit_Framework_TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Segment::CLASS);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\SynopsesTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = new Segment('pid', 'type');

        $this->assertSame(null, $entity->getId());
        $this->assertSame('pid', $entity->getPid());
        $this->assertSame('type', $entity->getType());
        $this->assertSame(null, $entity->getTitle());
        $this->assertSame(null, $entity->getDuration());
        $this->assertSame(null, $entity->getMusicRecordId());
        $this->assertSame(null, $entity->getReleaseTitle());
        $this->assertSame(null, $entity->getCatalogueNumber());
        $this->assertSame(null, $entity->getRecordLabel());
        $this->assertSame(null, $entity->getPublisher());
        $this->assertSame(null, $entity->getTrackNumber());
        $this->assertSame(null, $entity->getTrackSide());
        $this->assertSame(null, $entity->getSourceMedia());
        $this->assertSame(null, $entity->getMusicCode());
        $this->assertSame(null, $entity->getRecordingDate());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new Segment('pid', 'type');

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Pid', 'newPid'],
            ['Type', 'newType'],
            ['Title', 'newTitle'],
            ['Duration', 1],
            ['MusicRecordId', 'newMusicRecordId'],
            ['ReleaseTitle', 'newReleaseTitle'],
            ['CatalogueNumber', 'newCatalogueNumber'],
            ['RecordLabel', 'newRecordLabel'],
            ['Publisher', 'newPublisher'],
            ['TrackNumber', 'newTrackNumber'],
            ['TrackSide', 'newTrackSide'],
            ['SourceMedia', 'newSourceMedia'],
            ['MusicCode', 'newMusicCode'],
            ['RecordingDate', 'newRecordingDate'],
        ];
    }
}
