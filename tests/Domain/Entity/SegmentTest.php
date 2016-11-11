<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use PHPUnit_Framework_TestCase;

class SegmentTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');

        $segment = new Segment(
            0,
            $pid,
            'Type',
            $synopses,
            22,
            'Title'
        );

        $this->assertSame(0, $segment->getDbId());
        $this->assertSame($pid, $segment->getPid());
        $this->assertSame('Type', $segment->getType());
        $this->assertSame(22, $segment->getContributionCount());
        $this->assertSame('Title', $segment->getTitle());
        $this->assertSame($synopses, $segment->getSynopses());
        $this->assertNull($segment->getDuration());
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');

        $segment = new Segment(
            0,
            $pid,
            'Type',
            $synopses,
            22,
            'Title',
            1,
            []
        );

        $this->assertSame(1, $segment->getDuration());
        $this->assertSame([], $segment->getContributions());
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testGetContributionsNoContributions()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');

        $segment = new Segment(
            0,
            $pid,
            'Type',
            $synopses,
            22,
            'Title',
            1,
            null
        );

        $segment->getContributions();
    }
}
