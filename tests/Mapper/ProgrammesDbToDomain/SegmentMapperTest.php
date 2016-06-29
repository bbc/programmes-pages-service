<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\SegmentMapper;
use BBC\ProgrammesPagesService\Domain\Entity\MusicSegment;
use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use PHPUnit_Framework_TestCase;

class SegmentMapperTest extends PHPUnit_Framework_TestCase
{
    public function testGetDomainModelForSegment()
    {
        $dbEntityArray = [
            'id' => '1',
            'pid' => 'p01r8fvg',
            'type' => 'speech',
            'title' => 'Title',
            'duration' => 1,
            'shortSynopsis' => 'ShortSynopsis',
            'mediumSynopsis' => 'MediumSynopsis',
            'longSynopsis' => 'LongSynopsis',
        ];

        $expectedEntity = new Segment(
            new Pid('p01r8fvg'),
            'speech',
            'Title',
            new Synopses('ShortSynopsis', 'MediumSynopsis', 'LongSynopsis'),
            1
        );

        $mapper = new SegmentMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelForMusicSegment()
    {
        $dbEntityArray = [
            'id' => '1',
            'pid' => 'p01r8fvg',
            'type' => 'music',
            'title' => 'Title',
            'duration' => 1,
            'musicRecordId' => 'musicRecordId',
            'releaseTitle' => 'releaseTitle',
            'catalogueNumber' => 'catalogueNumber',
            'recordLabel' => 'recordLabel',
            'publisher' => 'publisher',
            'trackNumber' => 'trackNumber',
            'trackSide' => 'trackSide',
            'sourceMedia' => 'sourceMedia',
            'musicCode' => 'musicCode',
            'recordingDate' => 'recordingDate',
            'shortSynopsis' => 'ShortSynopsis',
            'mediumSynopsis' => 'MediumSynopsis',
            'longSynopsis' => 'LongSynopsis',
        ];

        $expectedEntity = new MusicSegment(
            new Pid('p01r8fvg'),
            'music',
            'Title',
            new Synopses('ShortSynopsis', 'MediumSynopsis', 'LongSynopsis'),
            1,
            'musicRecordId',
            'releaseTitle',
            'catalogueNumber',
            'recordLabel',
            'publisher',
            'trackNumber',
            'trackSide',
            'sourceMedia',
            'musicCode',
            'recordingDate'
        );

        $mapper = new SegmentMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));
    }

    /**
     * @dataProvider domainModelTypeDataProvider
     */
    public function testGetDomainModelReturnsCorrectClassesBasedOnType(string $type, string $expectedClass)
    {
        $dbEntityArray = [
            'id' => '1',
            'pid' => 'p01r8fvg',
            'type' => $type,
            'title' => 'Title',
            'duration' => 1,
            'musicRecordId' => 'musicRecordId',
            'releaseTitle' => 'releaseTitle',
            'catalogueNumber' => 'catalogueNumber',
            'recordLabel' => 'recordLabel',
            'publisher' => 'publisher',
            'trackNumber' => 'trackNumber',
            'trackSide' => 'trackSide',
            'sourceMedia' => 'sourceMedia',
            'musicCode' => 'musicCode',
            'recordingDate' => 'recordingDate',
            'shortSynopsis' => 'ShortSynopsis',
            'mediumSynopsis' => 'MediumSynopsis',
            'longSynopsis' => 'LongSynopsis',
        ];

        $mapper = new SegmentMapper();
        $this->assertInstanceOf($expectedClass, $mapper->getDomainModel($dbEntityArray));
    }

    public function domainModelTypeDataProvider()
    {
        return [
            ['speech', Segment::CLASS],
            ['chapter', Segment::CLASS],
            ['highlight', Segment::CLASS],
            ['other', Segment::CLASS],
            ['music', MusicSegment::CLASS],
            ['classical', MusicSegment::CLASS],
        ];
    }
}
