<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Entity\VersionType;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use DateTimeImmutable;
use PHPUnit_Framework_TestCase;

class VersionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('p01m5mss');
        $episode = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Episode');

        $version = new Version(0, $pid, $episode, true, false, 1);

        $this->assertEquals(0, $version->getDbId());
        $this->assertEquals($pid, $version->getPid());
        $this->assertEquals($episode, $version->getProgrammeItem());
        $this->assertEquals(true, $version->isStreamable());
        $this->assertEquals(false, $version->isDownloadable());
        $this->assertEquals(1, $version->getSegmentEventCount());
        $this->assertEquals(false, $version->hasCompetitionWarning());
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $episode = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Episode');
        $versionType = new VersionType('original', 'Original version');

        $streamableFrom = new DateTimeImmutable();
        $streamableUntil = new DateTimeImmutable('00:00:00 01/01/1970');

        $version = new Version(
            0,
            $pid,
            $episode,
            true,
            false,
            1,
            101,
            'GuidanceWarnings',
            true,
            $streamableFrom,
            $streamableUntil,
            [$versionType]
        );

        $this->assertEquals(101, $version->getDuration());
        $this->assertEquals('GuidanceWarnings', $version->getGuidanceWarningCodes());
        $this->assertEquals(true, $version->hasCompetitionWarning());
        $this->assertEquals([$versionType], $version->getVersionTypes());
        $this->assertEquals($streamableFrom, $version->getStreamableFrom());
        $this->assertEquals($streamableUntil, $version->getStreamableUntil());
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get VersionTypes of Version "p01m5mss" as they were not fetched
     */
    public function testRequestingUnfetchedVersionThrowsException()
    {
        $pid = new Pid('p01m5mss');
        $episode = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Episode');

        $version = new Version(0, $pid, $episode, true, false, 1);

        $version->getVersionTypes();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage versionTypes must be an array containing only instance of "BBC\ProgrammesPagesService\Domain\Entity\VersionType". Found instance of "string"
     */
    public function testInvalidVersionTypeUseScalar()
    {
        $pid = new Pid('p01m5mss');

        $programmeItem = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Episode');

        new Version(
            0,
            $pid,
            $programmeItem,
            true,
            true,
            1,
            101,
            'GuidanceWarnings',
            true,
            null,
            null,
            ['I am not a VersionType']
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage versionTypes must be an array containing only instance of "BBC\ProgrammesPagesService\Domain\Entity\VersionType". Found instance of "BBC\ProgrammesPagesService\Domain\ValueObject\Pid"
     */
    public function testInvalidVersionTypeUseObject()
    {
        $pid = new Pid('p01m5mss');

        $programmeItem = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Episode');

        new Version(
            0,
            $pid,
            $programmeItem,
            true,
            true,
            1,
            101,
            'GuidanceWarnings',
            true,
            null,
            null,
            [$pid]
        );
    }
}
