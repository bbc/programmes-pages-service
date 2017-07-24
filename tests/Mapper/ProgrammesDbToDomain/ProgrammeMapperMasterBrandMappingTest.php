<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedMasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedOptions;

class ProgrammeMapperMasterBrandMappingTest extends BaseProgrammeMapperTestCase
{
    public function testGetDomainModelSeriesWithSetMasterBrand()
    {
        $masterBrandDbEntity = [
            'mid' => 'bbc_one',
            'network' => null,
        ];

        $expectedMasterBrandDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\MasterBrand'
        );

        $this->mockMasterBrandMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($masterBrandDbEntity)
            ->willReturn($expectedMasterBrandDomainEntity);

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b010t19z',
            null,
            $masterBrandDbEntity
        );

        $expectedEntity = $this->getSampleProgrammeDomainEntity(
            'b010t19z',
            $this->mockDefaultImage,
            $expectedMasterBrandDomainEntity
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModelForProgramme($dbEntityArray));
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
            new UnfetchedMasterBrand(),
            [],
            [],
            null,
            1,
            new UnfetchedOptions()
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModelForProgramme($dbEntityArray));
    }
}
