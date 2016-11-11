<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Entity\VersionType;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use \DateTime;

class VersionMapperTest extends BaseMapperTestCase
{
    protected $mockProgrammeMapper;

    public function setUp()
    {
        $this->mockProgrammeMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper'
        );
    }

    public function testGetDomainModel()
    {
        $programmeDbEntity = ['pid' => 'p01m5mss'];

        $expectedProgrammeDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem'
        );

        $this->mockProgrammeMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($programmeDbEntity)
            ->willReturn($expectedProgrammeDomainEntity);


        $streamableFrom = new DateTime();
        $streamableUntil = new DateTime('00:00:00 01/01/1970');

        $dbEntityArray = [
            'id' => 1,
            'pid' => 'b0007c3v',
            'duration' => '360',
            'guidanceWarningCodes' => 'warnings',
            'competitionWarning' => true,
            'segmentEventCount' => 2,
            'streamable' => true,
            'downloadable' => false,
            'contributionCount' => 22,
            'streamableFrom' => $streamableFrom,
            'streamableUntil' => $streamableUntil,
            'programmeItem' => $programmeDbEntity,
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
        $expectedEntity = new Version(
            1,
            $pid,
            $expectedProgrammeDomainEntity,
            true,
            false,
            2,
            22,
            360,
            'warnings',
            true,
            \DateTimeImmutable::createFromMutable($streamableFrom),
            \DateTimeImmutable::createFromMutable($streamableUntil),
            $expectedVersionTypes
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage All versions must be joined to a ProgrammeItem
     */
    public function testGetDomainModelWithNoProgramme()
    {
        $dbEntityArray = [
            'id' => 1,
            'pid' => 'b0007c3v',
            'duration' => '360',
            'guidanceWarningCodes' => 'warnings',
            'competitionWarning' => true,
            'segmentEventCount' => 2,
            'streamable' => true,
            'downloadable' => false,
            'streamableFrom' => null,
            'streamableUntil' => null,
            'contributionCount' => 22,
            'versionTypes' => [],
        ];

        $this->getMapper()->getDomainModel($dbEntityArray);
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testGetDomainModelWithNoVersionTypes()
    {
        $programmeDbEntity = ['pid' => 'p01m5mss'];

        $expectedProgrammeDomainEntity = $this->createMock(
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
            'segmentEventCount' => 2,
            'streamable' => true,
            'downloadable' => false,
            'streamableFrom' => null,
            'streamableUntil' => null,
            'contributionCount' => 22,
            'programmeItem' => $programmeDbEntity,
        ];

        $this->getMapper()->getDomainModel($dbEntityArray)->getVersionTypes();
    }

    private function getMapper(): VersionMapper
    {
        return new VersionMapper($this->getMapperFactory([
            'ProgrammeMapper' => $this->mockProgrammeMapper,
        ]));
    }
}
