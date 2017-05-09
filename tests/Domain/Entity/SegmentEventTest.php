<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\SegmentEvent;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use PHPUnit\Framework\TestCase;

class SegmentEventTest extends TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('p01m5mss');
        $version = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Version');
        $segment = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Segment');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');

        $segmentEvent = new SegmentEvent(
            $pid,
            $version,
            $segment,
            $synopses,
            'Title'
        );

        $this->assertSame($pid, $segmentEvent->getPid());
        $this->assertSame($version, $segmentEvent->getVersion());
        $this->assertSame($segment, $segmentEvent->getSegment());
        $this->assertSame('Title', $segmentEvent->getTitle());
        $this->assertSame($synopses, $segmentEvent->getSynopses());
        $this->assertFalse($segmentEvent->isChapter());
        $this->assertNull($segmentEvent->getOffset());
        $this->assertNull($segmentEvent->getPosition());
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $version = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Version');
        $segment = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Segment');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');

        $segmentEvent = new SegmentEvent(
            $pid,
            $version,
            $segment,
            $synopses,
            'Title',
            true,
            1,
            2
        );

        $this->assertSame(true, $segmentEvent->isChapter());
        $this->assertSame(1, $segmentEvent->getOffset());
        $this->assertSame(2, $segmentEvent->getPosition());
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get Version of SegmentEvent "p01m5mss" as it was not fetched
     */
    public function testRequestingUnfetchedVersionThrowsException()
    {
        $segmentEvent = new SegmentEvent(
            new Pid('p01m5mss'),
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion'),
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Segment'),
            new Synopses('Short Synopsis', 'Longest Synopsis', ''),
            'Title'
        );

        $segmentEvent->getVersion();
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get Segment of SegmentEvent "p01m5mss" as it was not fetched
     */
    public function testRequestingUnfetchedSegmentThrowsException()
    {
        $segmentEvent = new SegmentEvent(
            new Pid('p01m5mss'),
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Version'),
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedSegment'),
            new Synopses('Short Synopsis', 'Longest Synopsis', ''),
            'Title'
        );

        $segmentEvent->getSegment();
    }
}
