<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MasterBrandMapper;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;
use PHPUnit_Framework_TestCase;

class MasterBrandMapperTest extends BaseMapperTestCase
{
    protected $mockImageMapper;

    protected $mockDefaultImage;

    protected $mockNetworkMapper;

    protected $mockVersionMapper;

    public function setUp()
    {
        $this->mockImageMapper = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ImageMapper'
        );

        $this->mockNetworkMapper = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\NetworkMapper'
        );

        $this->mockVersionMapper = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper'
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
            'mid' => 'bbc_three',
            'name' => 'Three',
        ];

        $mid = new Mid('bbc_three');
        $expectedEntity = new MasterBrand($mid, 'Three', $this->mockDefaultImage);

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
            'mid' => 'bbc_three',
            'name' => 'Three',
            'image' => $imageDbEntity,
        ];

        $mid = new Mid('bbc_three');
        $expectedEntity = new MasterBrand($mid, 'Three', $expectedImageDomainEntity);

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithSetNetwork()
    {
        $networkDbEntity = ['nid' => 'bbc_one'];

        $expectedNetworkDomainEntity = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Network'
        );

        $this->mockNetworkMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($networkDbEntity)
            ->willReturn($expectedNetworkDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'mid' => 'bbc_three',
            'name' => 'Three',
            'network' => $networkDbEntity,
        ];

        $mid = new Mid('bbc_three');
        $expectedEntity = new MasterBrand(
            $mid,
            'Three',
            $this->mockDefaultImage,
            $expectedNetworkDomainEntity,
            null
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithSetCompetitionWarning()
    {
        $versionDbEntity = ['pid' => 'p01m5mss'];

        $expectedVersionDomainEntity = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Version'
        );

        $this->mockVersionMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($versionDbEntity)
            ->willReturn($expectedVersionDomainEntity);

        $dbEntityArray = [
            'id' => 1,
            'mid' => 'bbc_three',
            'name' => 'Three',
            'competitionWarning' => $versionDbEntity,
        ];

        $expectedEntity = new MasterBrand(
            new Mid('bbc_three'),
            'Three',
            $this->mockDefaultImage,
            null,
            $expectedVersionDomainEntity
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }


    private function getMapper(): MasterBrandMapper
    {
        return new MasterBrandMapper($this->getMapperProvider([
            'ImageMapper' => $this->mockImageMapper,
            'NetworkMapper' => $this->mockNetworkMapper,
            'VersionMapper' => $this->mockVersionMapper,
        ]));
    }
}
