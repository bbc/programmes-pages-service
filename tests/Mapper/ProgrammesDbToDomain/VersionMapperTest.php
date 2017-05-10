<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Entity\VersionType;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper;
use DateTime;
use DateTimeImmutable;

class VersionMapperTest extends BaseMapperTestCase
{
    protected $mockProgrammeMapper;

    public function setUp()
    {
        $this->mockProgrammeMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper'
        );

        $this->mockVersionTypeMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionTypeMapper'
        );
    }

    public function testGetDomainModel()
    {
        $programmeDbEntity = ['pid' => 'p01m5mss'];

        $versionTypeDbEntities = [
            ['type' => 'original', 'name' => 'Original Version'],
            ['type' => 'ad', 'name' => 'Audio Described'],
        ];

        $expectedProgrammeDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem'
        );

        $expectedVersionTypes = [
            new VersionType('original', 'Original Version'),
            new VersionType('ad', 'Audio Described'),
        ];

        $this->mockProgrammeMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($programmeDbEntity)
            ->willReturn($expectedProgrammeDomainEntity);


        $this->mockVersionTypeMapper->expects($this->exactly(2))
            ->method('getDomainModel')
            ->withConsecutive(
                [$versionTypeDbEntities[0]],
                [$versionTypeDbEntities[1]]
            )
            ->will($this->onConsecutiveCalls(...$expectedVersionTypes));


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
            'contributionsCount' => 22,
            'streamableFrom' => $streamableFrom,
            'streamableUntil' => $streamableUntil,
            'programmeItem' => $programmeDbEntity,
            'versionTypes' => $versionTypeDbEntities,
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
            DateTimeImmutable::createFromMutable($streamableFrom),
            DateTimeImmutable::createFromMutable($streamableUntil),
            $expectedVersionTypes
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModel($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
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
            'contributionsCount' => 22,
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
            'contributionsCount' => 22,
            'programmeItem' => $programmeDbEntity,
        ];

        $this->getMapper()->getDomainModel($dbEntityArray)->getVersionTypes();
    }

    private function getMapper(): VersionMapper
    {
        return new VersionMapper($this->getMapperFactory([
            'ProgrammeMapper' => $this->mockProgrammeMapper,
            'VersionTypeMapper' => $this->mockVersionTypeMapper,
        ]));
    }
}
