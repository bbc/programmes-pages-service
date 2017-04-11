<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ServiceMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedNetwork;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use DateTime;
use DateTimeImmutable;
use PHPUnit_Framework_TestCase;

class ServiceMapperTest extends BaseMapperTestCase
{
    protected $mockNetworkMapper;

    public function setUp()
    {
        $this->mockNetworkMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\NetworkMapper'
        );
    }

    public function testGetDomainModel()
    {
        $dbEntityArray = [
            'id' => 1,
            'sid' => 'radio_four_fm',
            'pid' => 'b0000001',
            'type' => 'National Radio',
            'name' => 'Radio Four',
            'shortName' => 'FM',
            'urlKey' => 'fm',
            'mediaType' => 'audio',
            'network' => null,
            'startDate' => new DateTime('2015-01-03'),
            'endDate' => new DateTime('2015-01-04'),
            'liveStreamUrl' => 'liveStream',
        ];

        $expectedEntity = new Service(
            1,
            new Sid('radio_four_fm'),
            new Pid('b0000001'),
            'Radio Four',
            'FM',
            'fm',
            null,
            new DateTimeImmutable('2015-01-03'),
            new DateTimeImmutable('2015-01-04'),
            'liveStream'
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

    public function testGetDomainModelWithSetNetwork()
    {
        $networkDbEntity = ['nid' => 'radio_four'];

        $expectedNetworkDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Network'
        );

        $this->mockNetworkMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($networkDbEntity)
            ->willReturn($expectedNetworkDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'sid' => 'radio_four_fm',
            'pid' => 'b0000001',
            'type' => 'National Radio',
            'name' => 'Radio Four',
            'shortName' => 'FM',
            'urlKey' => 'fm',
            'mediaType' => 'audio',
            'network' => $networkDbEntity,
            'startDate' => new DateTime('2015-01-03'),
            'endDate' => new DateTime('2015-01-04'),
            'liveStreamUrl' => 'liveStream',
        ];

        $expectedEntity = new Service(
            1,
            new Sid('radio_four_fm'),
            new Pid('b0000001'),
            'Radio Four',
            'FM',
            'fm',
            $expectedNetworkDomainEntity,
            new DateTimeImmutable('2015-01-03'),
            new DateTimeImmutable('2015-01-04'),
            'liveStream'
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithUnfetchedNetwork()
    {
        $dbEntityArray = [
            'id' => 1,
            'sid' => 'radio_four_fm',
            'pid' => 'b0000001',
            'type' => 'National Radio',
            'name' => 'Radio Four',
            'shortName' => 'FM',
            'urlKey' => 'fm',
            'mediaType' => 'audio',
            'startDate' => new DateTime('2015-01-03'),
            'endDate' => new DateTime('2015-01-04'),
            'liveStreamUrl' => 'liveStream',
        ];

        $expectedEntity = new Service(
            1,
            new Sid('radio_four_fm'),
            new Pid('b0000001'),
            'Radio Four',
            'FM',
            'fm',
            new UnfetchedNetwork(),
            new DateTimeImmutable('2015-01-03'),
            new DateTimeImmutable('2015-01-04'),
            'liveStream'
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

    private function getMapper(): ServiceMapper
    {
        return new ServiceMapper($this->getMapperFactory([
            'NetworkMapper' => $this->mockNetworkMapper,
        ]));
    }
}
