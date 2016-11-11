<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\MusicSegment;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use PHPUnit_Framework_TestCase;

class MusicSegmentTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');

        $segment = new MusicSegment(
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
        $this->assertSame('Title', $segment->getTitle());
        $this->assertSame($synopses, $segment->getSynopses());
        $this->assertSame(22, $segment->getContributionCount());
        $this->assertNull($segment->getDuration());

        $this->assertNull($segment->getMusicRecordId());
        $this->assertNull($segment->getReleaseTitle());
        $this->assertNull($segment->getCatalogueNumber());
        $this->assertNull($segment->getRecordLabel());
        $this->assertNull($segment->getPublisher());
        $this->assertNull($segment->getTrackNumber());
        $this->assertNull($segment->getTrackSide());
        $this->assertNull($segment->getSourceMedia());
        $this->assertNull($segment->getMusicCode());
        $this->assertNull($segment->getRecordingDate());
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');

        $segment = new MusicSegment(
            0,
            $pid,
            'Type',
            $synopses,
            22,
            'Title',
            1,
            [],
            'MusicRecordId',
            'ReleaseTitle',
            'CatalogueNumber',
            'RecordLabel',
            'Publisher',
            'TrackNumber',
            'TrackSide',
            'SourceMedia',
            'MusicCode',
            'RecordingDate'
        );

        $this->assertSame(1, $segment->getDuration());
        $this->assertSame([], $segment->getContributions());
        $this->assertSame('MusicRecordId', $segment->getMusicRecordId());
        $this->assertSame('ReleaseTitle', $segment->getReleaseTitle());
        $this->assertSame('CatalogueNumber', $segment->getCatalogueNumber());
        $this->assertSame('RecordLabel', $segment->getRecordLabel());
        $this->assertSame('Publisher', $segment->getPublisher());
        $this->assertSame('TrackNumber', $segment->getTrackNumber());
        $this->assertSame('TrackSide', $segment->getTrackSide());
        $this->assertSame('SourceMedia', $segment->getSourceMedia());
        $this->assertSame('MusicCode', $segment->getMusicCode());
        $this->assertSame('RecordingDate', $segment->getRecordingDate());
    }
}
