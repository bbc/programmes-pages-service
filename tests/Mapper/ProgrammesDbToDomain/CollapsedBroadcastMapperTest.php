<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CollapsedBroadcastMapper;
use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;

class CollapsedBroadcastMapperTest extends BaseMapperTestCase
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

        $serviceDbEntity = ['mid' => 'bbc_one', 'sid' => 'a'];
        $services['a'] = $serviceDbEntity;

        $expectedVersionDomainEntity = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Version');

        $expectedProgrammeItemDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem'
        );

        $expectedServiceDomainEntity = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Service');

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
            'id'               => 1,
            'startAt'          => new DateTime('2015-01-03T00:00:00'),
            'endAt'            => new DateTime('2015-01-03T01:00:00'),
            'duration'         => 120,
            'isLive'           => false,
            'isBlanked'        => true,
            'isRepeat'         => true,
            'isCritical'       => false,
            'isAudioDescribed' => false,
            'isWebcast'        => false,
            'version'          => $versionDbEntity,
            'programmeItem'    => $programmeItemDbEntity,
            'serviceIds'       => ['a'],
        ];

        $expectedEntity = new CollapsedBroadcast(
            $expectedVersionDomainEntity,
            $expectedProgrammeItemDomainEntity,
            [$expectedServiceDomainEntity],
            new DateTimeImmutable('2015-01-03T00:00:00'),
            new DateTimeImmutable('2015-01-03T01:00:00'),
            120,
            true,
            true
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray, $services));
    }

    public function testGetDomainModelTakingProgrammeItemFromVersion()
    {
        $programmeItemDbEntity = ['pid' => 'b007b5xt'];
        $versionDbEntity = ['pid' => 'b03szzzz', 'programmeItem' => $programmeItemDbEntity];
        $serviceDbEntity = ['mid' => 'bbc_one'];
        $services['a'] = $serviceDbEntity;

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
            'id'               => 1,
            'startAt'          => new DateTime('2015-01-03T00:00:00'),
            'endAt'            => new DateTime('2015-01-03T01:00:00'),
            'duration'         => 120,
            'isLive'           => false,
            'isBlanked'        => true,
            'isRepeat'         => true,
            'isCritical'       => false,
            'isAudioDescribed' => false,
            'isWebcast'        => false,
            'version'          => $versionDbEntity,
            'serviceIds'       => ['a'],
        ];

        $expectedEntity = new CollapsedBroadcast(
            $expectedVersionDomainEntity,
            $expectedProgrammeItemDomainEntity,
            [$expectedServiceDomainEntity],
            new DateTimeImmutable('2015-01-03T00:00:00'),
            new DateTimeImmutable('2015-01-03T01:00:00'),
            120,
            true,
            true
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray, $services));
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testGetDomainModelWithNoVersion()
    {
        $serviceDbEntity = ['mid' => 'bbc_one'];
        $services['a'] = $serviceDbEntity;

        $dbEntityArray = [
            'id'               => 1,
            'startAt'          => new DateTime(),
            'endAt'            => new DateTime(),
            'duration'         => 120,
            'isLive'           => false,
            'isBlanked'        => false,
            'isRepeat'         => false,
            'isCritical'       => false,
            'isAudioDescribed' => false,
            'isWebcast'        => false,
            'programmeItem'    => ['pid' => 'b007b5xt'],
            'serviceIds'       => ['a'],
        ];

        $this->getMapper()->getDomainModel($dbEntityArray, $services)->getVersion();
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testGetDomainModelWithNoProgrammeItem()
    {
        $serviceDbEntity = ['mid' => 'bbc_one'];
        $services['a'] = $serviceDbEntity;

        $dbEntityArray = [
            'id'               => 1,
            'startAt'          => new DateTime(),
            'endAt'            => new DateTime(),
            'duration'         => 120,
            'isLive'           => false,
            'isBlanked'        => false,
            'isRepeat'         => false,
            'isCritical'       => false,
            'isAudioDescribed' => false,
            'isWebcast'        => false,
            'version'          => ['pid' => 'b03szzzz'],
            'serviceIds'       => ['a'],
        ];

        $this->getMapper()->getDomainModel($dbEntityArray, $services)->getProgrammeItem();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetDomainModelWithNoService()
    {
        $dbEntityArray = [
            'id'               => 1,
            'startAt'          => new DateTime(),
            'endAt'            => new DateTime(),
            'duration'         => 120,
            'isLive'           => false,
            'isBlanked'        => false,
            'isRepeat'         => false,
            'isCritical'       => false,
            'isAudioDescribed' => false,
            'isWebcast'        => false,
            'version'          => ['pid' => 'b03szzzz'],
            'serviceIds'       => ['a'],
        ];

        $this->getMapper()->getDomainModel($dbEntityArray)->getServices();
    }

    private function getMapper(): CollapsedBroadcastMapper
    {
        return new CollapsedBroadcastMapper(
            $this->getMapperFactory(
                [
                    'VersionMapper'   => $this->mockVersionMapper,
                    'ProgrammeMapper' => $this->mockProgrammeMapper,
                    'ServiceMapper'   => $this->mockServiceMapper,
                ]
            )
        );
    }
}
