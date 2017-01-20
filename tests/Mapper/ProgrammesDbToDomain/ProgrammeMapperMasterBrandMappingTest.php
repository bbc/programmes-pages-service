<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedMasterBrand;

class ProgrammeMapperMasterBrandMappingTest extends BaseProgrammeMapperTestCase
{
    public function testGetDomainModelSeriesWithSetMasterBrand()
    {
        $programmeOptions = ['progOptions'];
        $networkOptions = ['networkOptions'];

        $masterBrandDbEntity = [
            'id' => 1,
            'mid' => 'bbc_one',
            'options' => $networkOptions,
        ];

        $expectedMasterBrandDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\MasterBrand'
        );

        $expectedOptionsEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Options'
        );

        $this->mockMasterBrandMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($masterBrandDbEntity)
            ->willReturn($expectedMasterBrandDomainEntity);

        $this->mockOptionsMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($programmeOptions, $networkOptions)
            ->willReturn($expectedOptionsEntity);

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b010t19z',
            null,
            $masterBrandDbEntity,
            [],
            null,
            1,
            $programmeOptions
        );

        $expectedEntity = $this->getSampleProgrammeDomainEntity(
            'b010t19z',
            $this->mockDefaultImage,
            $expectedMasterBrandDomainEntity,
            [],
            [],
            null,
            1,
            $expectedOptionsEntity
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelSeriesWithUnfetchedMasterBrand()
    {
        $masterBrandDbEntity = null;

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b010t19z',
            null,
            null
        );
        unset($dbEntityArray['masterBrand']);

        $expectedEntity = $this->getSampleProgrammeDomainEntity(
            'b010t19z',
            $this->mockDefaultImage,
            new UnfetchedMasterBrand()
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }
}
