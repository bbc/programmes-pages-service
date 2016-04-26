<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Entity\VersionType;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use PHPUnit_Framework_TestCase;

class VersionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('p01m5mss');

        $image = new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $episode = new Episode(
            new Pid('p0000001'),
            'Ep',
            'Ep',
            'Syn',
            'Syn',
            $image,
            0,
            0,
            false,
            false,
            'audio',
            0,
            0,
            0
        );

        $version = new Version($pid, $episode);

        $this->assertEquals($pid, $version->getPid());
        $this->assertEquals($episode, $version->getProgrammeItem());
        $this->assertEquals(false, $version->hasCompetitionWarning());
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $versionType = new VersionType('original', 'Original version');

        $programmeItem = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Episode'
        );

        $version = new Version(
            $pid,
            $programmeItem,
            101,
            'GuidanceWarnings',
            true,
            [$versionType]
        );

        $this->assertEquals(101, $version->getDuration());
        $this->assertEquals('GuidanceWarnings', $version->getGuidanceWarningCodes());
        $this->assertEquals(true, $version->hasCompetitionWarning());
        $this->assertEquals($programmeItem, $version->getProgrammeItem());
        $this->assertEquals([$versionType], $version->getVersionTypes());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage versionTypes must be an array containing only instance of "BBC\ProgrammesPagesService\Domain\Entity\VersionType". Found instance of "string"
     */
    public function testInvalidVersionTypeUseScalar()
    {
        $pid = new Pid('p01m5mss');

        $programmeItem = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Episode'
        );

        new Version(
            $pid,
            $programmeItem,
            101,
            'GuidanceWarnings',
            true,
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

        $programmeItem = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Episode'
        );

        new Version(
            $pid,
            $programmeItem,
            101,
            'GuidanceWarnings',
            true,
            [$pid]
        );
    }
}
