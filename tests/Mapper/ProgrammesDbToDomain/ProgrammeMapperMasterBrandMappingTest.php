<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedMasterBrand;

class ProgrammeMapperMasterBrandMappingTest extends BaseProgrammeMapperTestCase
{
    public function testGetDomainModelSeriesWithSetMasterBrand()
    {
        $programmeOptions = ['progOptions'];
        $mbOptions = ['mbOptions'];

        $masterBrandDbEntity = ['mid' => 'bbc_one', 'options' => $mbOptions];

        $expectedMasterBrandDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\MasterBrand'
        );

        $expectedOptionsEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\MasterBrand'
        );

        $this->mockMasterBrandMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($masterBrandDbEntity)
            ->willReturn($expectedMasterBrandDomainEntity);

        $this->mockOptionsMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($programmeOptions, $mbOptions)
            ->willReturn($expectedOptionsEntity);

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b010t19z',
            null,
            $masterBrandDbEntity,
            [],
            [],
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
