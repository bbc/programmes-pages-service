<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\NetworkMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use PHPUnit_Framework_TestCase;

class NetworkMapperTest extends BaseMapperTestCase
{
    protected $mockImageMapper;

    protected $mockDefaultImage;

    protected $mockNetworkMapper;

    public function setUp()
    {
        $this->mockImageMapper = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ImageMapper'
        );

        $this->mockServiceMapper = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ServiceMapper'
        );

        $this->mockDefaultImage = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Image'
        );

        $this->mockImageMapper->expects($this->any())
            ->method('getDefaultImage')
            ->willReturn($this->mockDefaultImage);
    }

    public function testGetDomainModel()
    {
        $dbEntityArray = [
            'id' => 1,
            'nid' => '1_xtra',
            'name' => '1 Xtra',
            'urlKey' => '1xtra',
            'type' => 'National Radio',
            'medium' => 'radio',
            'isPublicOutlet' => true,
            'isChildrens' => true,
            'isWorldServiceInternational' => true,
            'isInternational' => true,
            'isAllowedAdverts' => true,
        ];

        $expectedEntity = new Network(
            new Nid('1_xtra'),
            '1 Xtra',
            $this->mockDefaultImage,
            '1xtra',
            'National Radio',
            'radio',
            null,
            true,
            true,
            true,
            true,
            true
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithSetImage()
    {
        $imageDbEntity = ['pid' => 'p01m5mss'];

        $expectedImageDomainEntity = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Image'
        );

        $this->mockImageMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($imageDbEntity)
            ->willReturn($expectedImageDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'nid' => '1_xtra',
            'name' => '1 Xtra',
            'urlKey' => '1xtra',
            'type' => 'National Radio',
            'medium' => 'radio',
            'image' => $imageDbEntity,
            'isPublicOutlet' => true,
            'isChildrens' => true,
            'isWorldServiceInternational' => true,
            'isInternational' => true,
            'isAllowedAdverts' => true,
        ];

        $expectedEntity = new Network(
            new Nid('1_xtra'),
            '1 Xtra',
            $expectedImageDomainEntity,
            '1xtra',
            'National Radio',
            'radio',
            null,
            true,
            true,
            true,
            true,
            true
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithSetDefaultService()
    {
        $serviceDbEntity = ['sid' => 'bbc_one'];

        $expectedServiceDomainEntity = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Service'
        );

        $this->mockServiceMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($serviceDbEntity)
            ->willReturn($expectedServiceDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'nid' => '1_xtra',
            'name' => '1 Xtra',
            'urlKey' => '1xtra',
            'type' => 'National Radio',
            'medium' => 'radio',
            'defaultService' => $serviceDbEntity,
            'isPublicOutlet' => true,
            'isChildrens' => true,
            'isWorldServiceInternational' => true,
            'isInternational' => true,
            'isAllowedAdverts' => true,
        ];

        $expectedEntity = new Network(
            new Nid('1_xtra'),
            '1 Xtra',
            $this->mockDefaultImage,
            '1xtra',
            'National Radio',
            'radio',
            $expectedServiceDomainEntity,
            true,
            true,
            true,
            true,
            true
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    private function getMapper(): NetworkMapper
    {
        return new NetworkMapper($this->getMapperProvider([
            'ImageMapper' => $this->mockImageMapper,
            'ServiceMapper' => $this->mockServiceMapper,
        ]));
    }
}
