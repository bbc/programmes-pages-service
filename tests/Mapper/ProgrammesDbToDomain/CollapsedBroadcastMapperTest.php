<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CollapsedBroadcastMapper;
use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;

class CollapsedBroadcastMapperTest extends BaseMapperTestCase
{
    protected $mockProgrammeMapper;
    protected $mockServiceMapper;

    public function setUp()
    {
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
        $programmeItemDbEntity = ['pid' => 'b007b5xt'];

        $serviceDbEntity = ['mid' => 'bbc_one', 'sid' => 'a'];
        $services = ['a' => $serviceDbEntity];

        $expectedProgrammeItemDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem'
        );

        $expectedServiceDomainEntity = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Service');

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
            'programmeItem'    => $programmeItemDbEntity,
            'serviceIds'       => ['a'],
        ];

        $expectedEntity = new CollapsedBroadcast(
            $expectedProgrammeItemDomainEntity,
            [$expectedServiceDomainEntity],
            new DateTimeImmutable('2015-01-03T00:00:00'),
            new DateTimeImmutable('2015-01-03T01:00:00'),
            120,
            true,
            true
        );

        $mapper = $mapper = $this->getMapper();
        $this->assertEquals(
            $expectedEntity,
            $mapper->getDomainModel($dbEntityArray, $services)
        );
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testGetDomainModelWithNoProgrammeItem()
    {
        $serviceDbEntity = ['mid' => 'bbc_one'];
        $services = ['a' => $serviceDbEntity];

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
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage All CollapsedBroadcasts must be joined to at least one Service
     */
    public function testGetDomainModelWithNonExistentServiceIds()
    {
        $services = ['a' => ['mid' => 'bbc_one', 'sid' => 'a']];

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
            'version'          => ['pid' => 'b03szzzz'],
            'programmeItem'    => ['pid' => 'b007b5xt'],
        ];

        $this->getMapper()->getDomainModel($dbEntityArray, $services);
    }

    public function testStrippingWebcasts()
    {
        $programmeItemDbEntity = ['pid' => 'b007b5xt'];

        $serviceDbEntity1 = ['mid' => 'bbc_one', 'sid' => 'a', 'id' => '1'];
        $serviceDbEntity2 = ['mid' => 'bbc_one_scotland', 'sid' => 'b', 'id' => '3'];
        $services = [1 => $serviceDbEntity1, 3 => $serviceDbEntity2];

        $expectedProgrammeItemDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem'
        );

        $expectedServiceDomainEntity1 = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Service');
        $expectedServiceDomainEntity2 = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Service');

        $this->mockProgrammeMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($programmeItemDbEntity)
            ->willReturn($expectedProgrammeItemDomainEntity);

        $this->mockServiceMapper->expects($this->at(0))
            ->method('getDomainModel')
            ->with($serviceDbEntity1)
            ->willReturn($expectedServiceDomainEntity1);

        $this->mockServiceMapper->expects($this->at(1))
            ->method('getDomainModel')
            ->with($serviceDbEntity2)
            ->willReturn($expectedServiceDomainEntity2);

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
            'programmeItem'    => $programmeItemDbEntity,
            'broadcastIds'     => [1, 2, 3],
            'areWebcasts'      => [0, 1, 0],
            'serviceIds'       => [1, 11, 3],
        ];

        $expectedEntity = new CollapsedBroadcast(
            $expectedProgrammeItemDomainEntity,
            [$expectedServiceDomainEntity1, $expectedServiceDomainEntity2],
            new DateTimeImmutable('2015-01-03T00:00:00'),
            new DateTimeImmutable('2015-01-03T01:00:00'),
            120,
            true,
            true
        );

        $mapper = $mapper = $this->getMapper();
        $this->assertEquals(
            $expectedEntity,
            $mapper->getDomainModel($dbEntityArray, $services)
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetDomainModelWithNonExistentService()
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
            'serviceIds'       => ['a'],
        ];

        $this->getMapper()->getDomainModel($dbEntityArray)->getServices();
    }

    private function getMapper(): CollapsedBroadcastMapper
    {
        return new CollapsedBroadcastMapper(
            $this->getMapperFactory(
                [
                    'ProgrammeMapper' => $this->mockProgrammeMapper,
                    'ServiceMapper'   => $this->mockServiceMapper,
                ]
            )
        );
    }
}
