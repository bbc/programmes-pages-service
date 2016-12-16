<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\BroadcastMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use DateTime;
use DateTimeImmutable;

class BroadcastMapperTest extends BaseMapperTestCase
{
    protected $mockVersionMapper;

    protected $mockProgrammeMapper;

    protected $mockServiceMapper;

    public function setUp()
    {
        $this->mockVersionMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper'
        );

        $this->mockProgrammeMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper'
        );

        $this->mockProgrammeMapper->method('getDomainModel')->willReturn(
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem')
        );

        $this->mockServiceMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ServiceMapper'
        );
    }

    public function testGetDomainModel()
    {
        $versionDbEntity = ['pid' => 'b03szzzz'];
        $programmeItemDbEntity = ['pid' => 'b007b5xt'];
        $serviceDbEntity = ['mid' => 'bbc_one'];

        $expectedVersionDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Version'
        );

        $expectedProgrammeItemDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem'
        );

        $expectedServiceDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Service'
        );

        $this->mockVersionMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($versionDbEntity)
            ->willReturn($expectedVersionDomainEntity);

        $this->mockProgrammeMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($programmeItemDbEntity)
            ->willReturn($expectedProgrammeItemDomainEntity);

        $this->mockServiceMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($serviceDbEntity)
            ->willReturn($expectedServiceDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'pid' => 'p0000dyc',
            'startAt' => new DateTime('2015-01-03T00:00:00'),
            'endAt' => new DateTime('2015-01-03T01:00:00'),
            'duration' => 120,
            'isLive' => false,
            'isBlanked' => true,
            'isRepeat' => true,
            'isCritical' => false,
            'isAudioDescribed' => false,
            'isWebcast' => false,
            'version' => $versionDbEntity,
            'programmeItem' => $programmeItemDbEntity,
            'service' => $serviceDbEntity,
        ];

        $expectedEntity = new Broadcast(
            new Pid('p0000dyc'),
            $expectedVersionDomainEntity,
            $expectedProgrammeItemDomainEntity,
            $expectedServiceDomainEntity,
            new DateTimeImmutable('2015-01-03T00:00:00'),
            new DateTimeImmutable('2015-01-03T01:00:00'),
            120,
            true,
            true
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

    public function testGetDomainModelTakingProgrammeItemFromVersion()
    {
        $programmeItemDbEntity = ['pid' => 'b007b5xt'];
        $versionDbEntity = ['pid' => 'b03szzzz', 'programmeItem' => $programmeItemDbEntity];
        $serviceDbEntity = ['mid' => 'bbc_one'];

        $expectedVersionDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Version'
        );

        $expectedProgrammeItemDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem'
        );

        $expectedServiceDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Service'
        );

        $this->mockVersionMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($versionDbEntity)
            ->willReturn($expectedVersionDomainEntity);

        $this->mockProgrammeMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($programmeItemDbEntity)
            ->willReturn($expectedProgrammeItemDomainEntity);

        $this->mockServiceMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($serviceDbEntity)
            ->willReturn($expectedServiceDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'pid' => 'p0000dyc',
            'startAt' => new DateTime('2015-01-03T00:00:00'),
            'endAt' => new DateTime('2015-01-03T01:00:00'),
            'duration' => 120,
            'isLive' => false,
            'isBlanked' => true,
            'isRepeat' => true,
            'isCritical' => false,
            'isAudioDescribed' => false,
            'isWebcast' => false,
            'version' => $versionDbEntity,
            'service' => $serviceDbEntity,
        ];

        $expectedEntity = new Broadcast(
            new Pid('p0000dyc'),
            $expectedVersionDomainEntity,
            $expectedProgrammeItemDomainEntity,
            $expectedServiceDomainEntity,
            new DateTimeImmutable('2015-01-03T00:00:00'),
            new DateTimeImmutable('2015-01-03T01:00:00'),
            120,
            true,
            true
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testGetDomainModelWithNoVersion()
    {
        $dbEntityArray = [
            'id' => 1,
            'pid' => 'p0000dyc',
            'startAt' => new DateTime(),
            'endAt' => new DateTime(),
            'duration' => 120,
            'isLive' => false,
            'isBlanked' => false,
            'isRepeat' => false,
            'isCritical' => false,
            'isAudioDescribed' => false,
            'isWebcast' => false,
            'programmeItem' => ['pid' => 'b007b5xt'],
            'service' => ['mid' => 'bbc_one'],
        ];

        $this->getMapper()->getDomainModel($dbEntityArray)->getVersion();
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testGetDomainModelWithNoProgrammeItem()
    {
        $dbEntityArray = [
            'id' => 1,
            'pid' => 'p0000dyc',
            'startAt' => new DateTime(),
            'endAt' => new DateTime(),
            'duration' => 120,
            'isLive' => false,
            'isBlanked' => false,
            'isRepeat' => false,
            'isCritical' => false,
            'isAudioDescribed' => false,
            'isWebcast' => false,
            'version' => ['pid' => 'b03szzzz'],
            'service' => ['mid' => 'bbc_one'],
        ];

        $this->getMapper()->getDomainModel($dbEntityArray)->getProgrammeItem();
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testGetDomainModelWithNoService()
    {
        $dbEntityArray = [
            'id' => 1,
            'pid' => 'p0000dyc',
            'startAt' => new DateTime(),
            'endAt' => new DateTime(),
            'duration' => 120,
            'isLive' => false,
            'isBlanked' => false,
            'isRepeat' => false,
            'isCritical' => false,
            'isAudioDescribed' => false,
            'isWebcast' => false,
            'version' => ['pid' => 'b03szzzz'],
            'programmeItem' => ['pid' => 'b007b5xt'],
        ];

        $this->getMapper()->getDomainModel($dbEntityArray)->getService();
    }

    private function getMapper(): BroadcastMapper
    {
        return new BroadcastMapper($this->getMapperFactory([
            'VersionMapper' => $this->mockVersionMapper,
            'ProgrammeMapper' => $this->mockProgrammeMapper,
            'ServiceMapper' => $this->mockServiceMapper,
        ]));
    }
}
