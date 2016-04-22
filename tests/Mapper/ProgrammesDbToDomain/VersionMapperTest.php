<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Entity\VersionType;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use PHPUnit_Framework_TestCase;

class VersionMapperTest extends BaseMapperTestCase
{
    protected $mockProgrammeMapper;

    public function setUp()
    {
        $this->mockProgrammeMapper = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper'
        );
    }

    public function testGetDomainModel()
    {
        $dbEntityArray = [
            'id' => 1,
            'pid' => 'b0007c3v',
            'duration' => '360',
            'guidanceWarningCodes' => 'warnings',
            'competitionWarning' => true,
        ];

        $pid = new Pid('b0007c3v');
        $expectedEntity = new Version($pid, 360, 'warnings', true);

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithSetProgrammeItem()
    {
        $programmeDbEntity = ['pid' => 'p01m5mss'];

        $expectedProgrammeDomainEntity = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem'
        );

        $this->mockProgrammeMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($programmeDbEntity)
            ->willReturn($expectedProgrammeDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'pid' => 'b0007c3v',
            'duration' => '360',
            'guidanceWarningCodes' => 'warnings',
            'competitionWarning' => true,
            'programmeItem' => $programmeDbEntity,
        ];

        $pid = new Pid('b0007c3v');
        $expectedEntity = new Version($pid, 360, 'warnings', true, $expectedProgrammeDomainEntity);

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithSetVersionTypes()
    {
        $dbEntityArray = [
            'id' => 1,
            'pid' => 'b0007c3v',
            'duration' => '360',
            'guidanceWarningCodes' => 'warnings',
            'competitionWarning' => true,
            'versionTypes' => [
                ['type' => 'original', 'name' => 'Original Version'],
                ['type' => 'ad', 'name' => 'Audio Described'],
            ],
        ];

        $expectedVersionTypes = [
            new VersionType('original', 'Original Version'),
            new VersionType('ad', 'Audio Described'),
        ];

        $pid = new Pid('b0007c3v');
        $expectedEntity = new Version($pid, 360, 'warnings', true, null, $expectedVersionTypes);

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    private function getMapper(): VersionMapper
    {
        return new VersionMapper($this->getMapperProvider([
            'ProgrammeMapper' => $this->mockProgrammeMapper,
        ]));
    }
}
