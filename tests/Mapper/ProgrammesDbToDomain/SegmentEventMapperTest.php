<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\SegmentEventMapper;
use BBC\ProgrammesPagesService\Domain\Entity\SegmentEvent;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class SegmentEventMapperTest extends BaseMapperTestCase
{
    protected $mockVersionMapper;

    protected $mockSegmentMapper;

    public function setUp()
    {
        $this->mockVersionMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper'
        );

        $this->mockSegmentMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\SegmentMapper'
        );
    }

    public function testGetDomainModel()
    {
        $versionDbEntity = ['pid' => 'b03szzzz'];
        $segmentDbEntity = ['pid' => 'p01r8fvg'];

        $expectedVersionDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Version'
        );

        $expectedSegmentDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Segment'
        );

        $this->mockVersionMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($versionDbEntity)
            ->willReturn($expectedVersionDomainEntity);

        $this->mockSegmentMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($segmentDbEntity)
            ->willReturn($expectedSegmentDomainEntity);

        $dbEntityArray = [
            'pid' => 'p01r8fvz',
            'title' => 'Title',
            'offset' => 1,
            'position' => 2,
            'isChapter' => true,
            'shortSynopsis' => 'ShortSynopsis',
            'mediumSynopsis' => 'MediumSynopsis',
            'longSynopsis' => 'LongSynopsis',
            'version' => $versionDbEntity,
            'segment' => $segmentDbEntity,
        ];

        $expectedEntity = new SegmentEvent(
            new Pid('p01r8fvz'),
            $expectedVersionDomainEntity,
            $expectedSegmentDomainEntity,
            'Title',
            new Synopses('ShortSynopsis', 'MediumSynopsis', 'LongSynopsis'),
            true,
            1,
            2
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testGetDomainModelWithNoVersion()
    {
        $dbEntityArray = [
            'pid' => 'p01r8fvz',
            'title' => 'Title',
            'offset' => 1,
            'position' => 2,
            'isChapter' => true,
            'shortSynopsis' => 'ShortSynopsis',
            'mediumSynopsis' => 'MediumSynopsis',
            'longSynopsis' => 'LongSynopsis',
            'segment' => ['pid' => 'p01r8fvg'],
        ];

        $this->getMapper()->getDomainModel($dbEntityArray)->getVersion();
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testGetDomainModelWithNoSegment()
    {
        $dbEntityArray = [
            'pid' => 'p01r8fvz',
            'title' => 'Title',
            'offset' => 1,
            'position' => 2,
            'isChapter' => true,
            'shortSynopsis' => 'ShortSynopsis',
            'mediumSynopsis' => 'MediumSynopsis',
            'longSynopsis' => 'LongSynopsis',
            'version' => ['pid' => 'b03szzzz'],
        ];

        $this->getMapper()->getDomainModel($dbEntityArray)->getSegment();
    }

    private function getMapper(): SegmentEventMapper
    {
        return new SegmentEventMapper($this->getMapperFactory([
            'VersionMapper' => $this->mockVersionMapper,
            'SegmentMapper' => $this->mockSegmentMapper,
        ]));
    }
}
