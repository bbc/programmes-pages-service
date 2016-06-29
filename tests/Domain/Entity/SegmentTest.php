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
            $pid,
            'Type',
            'Title',
            $synopses
        );

        $this->assertSame($pid, $segment->getPid());
        $this->assertSame('Type', $segment->getType());
        $this->assertSame('Title', $segment->getTitle());
        $this->assertSame($synopses, $segment->getSynopses());
        $this->assertNull($segment->getDuration());
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');

        $segment = new Segment(
            $pid,
            'Type',
            'Title',
            $synopses,
            1
        );

        $this->assertSame(1, $segment->getDuration());
    }
}
