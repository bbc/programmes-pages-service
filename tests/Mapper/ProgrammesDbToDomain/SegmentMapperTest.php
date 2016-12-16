<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\SegmentMapper;
use BBC\ProgrammesPagesService\Domain\Entity\MusicSegment;
use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class SegmentMapperTest extends BaseMapperTestCase
{
    public function testGetDomainModelForSegment()
    {
        $dbEntityArray = [
            'id' => '1',
            'pid' => 'p01r8fvg',
            'type' => 'speech',
            'title' => 'Title',
            'contributionsCount' => 22,
            'duration' => 1,
            'shortSynopsis' => 'ShortSynopsis',
            'mediumSynopsis' => 'MediumSynopsis',
            'longSynopsis' => 'LongSynopsis',
            'contributions' => [],
        ];

        $expectedEntity = new Segment(
            1,
            new Pid('p01r8fvg'),
            'speech',
            new Synopses('ShortSynopsis', 'MediumSynopsis', 'LongSynopsis'),
            22,
            'Title',
            1,
            []
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModel($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    public function testGetDomainModelForMusicSegment()
    {
        $dbEntityArray = [
            'id' => '1',
            'pid' => 'p01r8fvg',
            'type' => 'music',
            'title' => 'Title',
            'contributionsCount' => 22,
            'duration' => 1,
            'contributions' => [],
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
            1,
            new Pid('p01r8fvg'),
            'music',
            new Synopses('ShortSynopsis', 'MediumSynopsis', 'LongSynopsis'),
            22,
            'Title',
            1,
            [],
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

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
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
            'contributionsCount' => 22,
            'contributions' => [],
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

        $this->assertInstanceOf($expectedClass, $this->getMapper()->getDomainModel($dbEntityArray));
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

    private function getMapper(): SegmentMapper
    {
        return new SegmentMapper($this->getMapperFactory([]));
    }
}
