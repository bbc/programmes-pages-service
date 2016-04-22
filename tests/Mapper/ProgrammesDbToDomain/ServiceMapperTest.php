<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ServiceMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTime;
use DateTimeImmutable;
use PHPUnit_Framework_TestCase;

class ServiceMapperTest extends BaseMapperTestCase
{
    protected $mockNetworkMapper;

    public function setUp()
    {
        $this->mockNetworkMapper = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\NetworkMapper'
        );
    }

    public function testGetDomainModel()
    {
        $dbEntityArray = [
            'id' => 1,
            'sid' => 'radio_four_fm',
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
            new Sid('radio_four_fm'),
            'Radio Four',
            'FM',
            'fm',
            null,
            new DateTimeImmutable('2015-01-03'),
            new DateTimeImmutable('2015-01-04'),
            'liveStream'
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithSetNetwork()
    {
        $networkDbEntity = ['nid' => 'radio_four'];

        $expectedNetworkDomainEntity = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Network'
        );

        $this->mockNetworkMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($networkDbEntity)
            ->willReturn($expectedNetworkDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'sid' => 'radio_four_fm',
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
            new Sid('radio_four_fm'),
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

    private function getMapper(): ServiceMapper
    {
        return new ServiceMapper($this->getMapperProvider([
            'NetworkMapper' => $this->mockNetworkMapper,
        ]));
    }
}
