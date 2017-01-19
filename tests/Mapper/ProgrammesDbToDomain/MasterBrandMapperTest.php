<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MasterBrandMapper;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedNetwork;
use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;

class MasterBrandMapperTest extends BaseMapperTestCase
{
    protected $mockImageMapper;

    protected $mockNetworkMapper;

    protected $mockOptionsMapper;

    protected $mockVersionMapper;

    protected $mockDefaultImage;

    protected $mockNetwork;

    protected $mockOptions;

    public function setUp()
    {
        $this->mockImageMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ImageMapper'
        );

        $this->mockNetworkMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\NetworkMapper'
        );

        $this->mockOptionsMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\OptionsMapper'
        );

        $this->mockVersionMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper'
        );

        $this->mockDefaultImage = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Image'
        );

        $this->mockNetwork = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Network'
        );

        $this->mockOptions = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Options'
        );

        $this->mockImageMapper->expects($this->any())
            ->method('getDefaultImage')
            ->willReturn($this->mockDefaultImage);
    }

    public function testGetDomainModel()
    {
        $networkDbEntity = ['nid' => 'bbc_one'];
        $optionsDbEntity = [
            'one' => [
                'value' => 1,
                'cascades' => false,
            ],
        ];

        $this->setupNetworkMapper($networkDbEntity, $this->mockNetwork);
        $this->setupOptionsMapper($optionsDbEntity, $this->mockOptions);

        $dbEntityArray = [
            'id' => 1,
            'mid' => 'bbc_three',
            'name' => 'Three',
            'network' => $networkDbEntity,
            'competitionWarning' => null,
            'options' => $optionsDbEntity,
        ];

        $mid = new Mid('bbc_three');
        $expectedEntity = new MasterBrand(
            $mid,
            'Three',
            $this->mockDefaultImage,
            $this->mockNetwork,
            new Options(['one' => 1]),
            null
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

    public function testGetDomainModelWithoutSetNetwork()
    {
        $dbEntityArray = [
            'id' => 1,
            'mid' => 'bbc_three',
            'name' => 'Three',
            'competitionWarning' => null,
        ];
        $this->setupOptionsMapper([], $this->mockOptions);

        $mid = new Mid('bbc_three');
        $expectedEntity = new MasterBrand(
            $mid,
            'Three',
            $this->mockDefaultImage,
            new UnfetchedNetwork(),
            $this->mockOptions
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithSetButNullNetworkReturnsNull()
    {
        $dbEntityArray = [
            'id' => 1,
            'mid' => 'bbc_three',
            'name' => 'Three',
            'network' => null,
            'competitionWarning' => null,
        ];

        $this->assertEquals(null, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithSetImage()
    {
        $networkDbEntity = ['nid' => 'bbc_one'];
        $imageDbEntity = ['pid' => 'p01m5mss'];

        $this->setupNetworkMapper($networkDbEntity, $this->mockNetwork);
        $this->setupOptionsMapper([], $this->mockOptions);

        $expectedImageDomainEntity = $this->createMock(
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
            'network' => $networkDbEntity,
            'image' => $imageDbEntity,
            'competitionWarning' => null,
        ];

        $mid = new Mid('bbc_three');
        $expectedEntity = new MasterBrand(
            $mid,
            'Three',
            $expectedImageDomainEntity,
            $this->mockNetwork,
            $this->mockOptions
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithSetCompetitionWarning()
    {
        $networkDbEntity = ['nid' => 'bbc_one'];
        $versionDbEntity = ['pid' => 'p01m5mss'];

        $this->setupNetworkMapper($networkDbEntity, $this->mockNetwork);
        $this->setupOptionsMapper([], $this->mockOptions);

        $expectedVersionDomainEntity = $this->createMock(
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
            'network' => $networkDbEntity,
            'competitionWarning' => $versionDbEntity,
        ];

        $expectedEntity = new MasterBrand(
            new Mid('bbc_three'),
            'Three',
            $this->mockDefaultImage,
            $this->mockNetwork,
            $this->mockOptions,
            $expectedVersionDomainEntity
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelWithoutFetchingCompetitionWarning()
    {
        $networkDbEntity = ['nid' => 'bbc_one'];

        $this->setupNetworkMapper($networkDbEntity, $this->mockNetwork);
        $this->setupOptionsMapper([], $this->mockOptions);

        $expectedVersionDomainEntity = new UnfetchedVersion();

        $dbEntityArray = [
            'id' => 1,
            'mid' => 'bbc_three',
            'name' => 'Three',
            'network' => $networkDbEntity,
        ];

        $expectedEntity = new MasterBrand(
            new Mid('bbc_three'),
            'Three',
            $this->mockDefaultImage,
            $this->mockNetwork,
            $this->mockOptions,
            $expectedVersionDomainEntity
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    private function setupNetworkMapper($expectedDbEntity, $result)
    {
        $this->mockNetworkMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($expectedDbEntity)
            ->willReturn($result);
    }

    private function setupOptionsMapper($expectedDbEntity, $result)
    {
        $this->mockOptionsMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($expectedDbEntity)
            ->willReturn($result);
    }

    private function getMapper(): MasterBrandMapper
    {
        return new MasterBrandMapper($this->getMapperFactory([
            'ImageMapper' => $this->mockImageMapper,
            'NetworkMapper' => $this->mockNetworkMapper,
            'VersionMapper' => $this->mockVersionMapper,
        ]));
    }
}
