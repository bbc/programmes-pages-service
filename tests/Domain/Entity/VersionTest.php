<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Entity\VersionType;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use PHPUnit_Framework_TestCase;

class VersionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('p01m5mss');

        $version = new Version($pid);

        $this->assertEquals($pid, $version->getPid());
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
            101,
            'GuidanceWarnings',
            true,
            $programmeItem,
            [$versionType]
        );

        $this->assertEquals(101, $version->getDuration());
        $this->assertEquals('GuidanceWarnings', $version->getGuidanceWarningCodes());
        $this->assertEquals(true, $version->hasCompetitionWarning());
        $this->assertEquals($programmeItem, $version->getProgrammeItem());
        $this->assertEquals([$versionType], $version->getVersionTypes());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage versionTypes must be an array containing only instance of "BBC\ProgrammesPagesService\Domain\Entity\VersionType". Found instance of "string"
     */
    public function testInvalidVersionTypeUseScalar()
    {
        $pid = new Pid('p01m5mss');

        $programmeItem = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Episode'
        );

        $version = new Version(
            $pid,
            101,
            'GuidanceWarnings',
            true,
            $programmeItem,
            ['I am not a VersionType']
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage versionTypes must be an array containing only instance of "BBC\ProgrammesPagesService\Domain\Entity\VersionType". Found instance of "BBC\ProgrammesPagesService\Domain\ValueObject\Pid"
     */
    public function testInvalidVersionTypeUseObject()
    {
        $pid = new Pid('p01m5mss');

        $programmeItem = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Episode'
        );

        $version = new Version(
            $pid,
            101,
            'GuidanceWarnings',
            true,
            $programmeItem,
            [$pid]
        );
    }
}
