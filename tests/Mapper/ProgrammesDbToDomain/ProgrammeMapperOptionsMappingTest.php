<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedMasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedOptions;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;

class ProgrammeMapperOptionsMappingTest extends BaseProgrammeMapperTestCase
{
    public function testGetDomainModelHierarchy()
    {
        $seriesOptions = ['seriesOptions'];
        $brandOptions = ['brandOptions'];
        $networkOptions = ['networkOptions'];

        $masterBrandDbEntity = [
            'id' => 1,
            'mid' => 'bbc_one',
            'network' => [
                'id' => 1,
                'options' => $networkOptions,
            ],
        ];

        $expectedMasterBrandDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\MasterBrand'
        );

        $this->mockMasterBrandMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($masterBrandDbEntity)
            ->willReturn($expectedMasterBrandDomainEntity);

        $expectedOptionsEntity1 = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Options'
        );

        $expectedOptionsEntity2 = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Options'
        );

        $this->mockOptionsMapper->expects($this->at(0))
            ->method('getDomainModel')
            ->with($seriesOptions, $brandOptions, $networkOptions)
            ->willReturn($expectedOptionsEntity1);

        $this->mockOptionsMapper->expects($this->at(1))
            ->method('getDomainModel')
            ->with($brandOptions, $networkOptions)
            ->willReturn($expectedOptionsEntity2);

        $parent = $this->getSampleProgrammeDbEntity(
            'b010t19z',
            null,
            $masterBrandDbEntity,
            [],
            null,
            2,
            $brandOptions
        );

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b00swyx1',
            null,
            null,
            [],
            $parent,
            1,
            $seriesOptions
        );

        $expectedParent = $this->getSampleProgrammeDomainEntity(
            'b010t19z',
            $this->mockDefaultImage,
            $expectedMasterBrandDomainEntity,
            [],
            [],
            null,
            2,
            $expectedOptionsEntity2
        );

        $expectedEntity = $this->getSampleProgrammeDomainEntity(
            'b00swyx1',
            $this->mockDefaultImage,
            null,
            [],
            [],
            $expectedParent,
            1,
            $expectedOptionsEntity1
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainUnfetchedParent()
    {
        $series = $this->getSampleProgrammeDbEntity(
            'b00swyx1',
            null,
            null,
            [],
            null,
            1
        );
        $expectedEntity = $this->getSampleProgrammeDomainEntity(
            'b00swyx1',
            $this->mockDefaultImage,
            null,
            [],
            [],
            new UnfetchedProgramme(),
            1,
            new UnfetchedOptions()
        );

        // parent is not set, so a fetch of it wasn't attempted
        unset($series['parent']);

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($series));
    }

    public function testGetDomainUnfetchedMasterBrand()
    {
        $tleo = $this->getSampleProgrammeDbEntity(
            'b010t19z',
            null,
            null,
            [],
            null,
            2
        );

        // masterBrand is not set, so a fetch of it wasn't attempted
        unset($tleo['masterBrand']);

        $series = $this->getSampleProgrammeDbEntity(
            'b00swyx1',
            null,
            null,
            [],
            $tleo,
            1
        );

        $expectedEntity = $this->getSampleProgrammeDomainEntity(
            'b00swyx1',
            $this->mockDefaultImage,
            null,
            [],
            [],
            $this->getSampleProgrammeDomainEntity(
                'b010t19z',
                $this->mockDefaultImage,
                new UnfetchedMasterBrand(),
                [],
                [],
                null,
                2,
                new UnfetchedOptions()
            ),
            1,
            new UnfetchedOptions()
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($series));
    }

    public function testGetDomainUnfetchedNetwork()
    {
        // network is not set, so a fetch of it wasn't attempted
        $masterBrand = [
            'id' => 1,
            'mid' => 'bbc_one',
        ];

        // Mock the master brand itself, so the code stays in this class
        $mockMasterBrand = $this->createMock(MasterBrand::class);
        $this->mockMasterBrandMapper->expects($this->once())
            ->method('getDomainModel')
            ->willReturn($mockMasterBrand);

        $tleo = $this->getSampleProgrammeDbEntity(
            'b010t19z',
            null,
            $masterBrand,
            [],
            null,
            2
        );

        $series = $this->getSampleProgrammeDbEntity(
            'b00swyx1',
            null,
            null,
            [],
            $tleo,
            1
        );

        $expectedEntity = $this->getSampleProgrammeDomainEntity(
            'b00swyx1',
            $this->mockDefaultImage,
            null,
            [],
            [],
            $this->getSampleProgrammeDomainEntity(
                'b010t19z',
                $this->mockDefaultImage,
                $mockMasterBrand,
                [],
                [],
                null,
                2,
                new UnfetchedOptions()
            ),
            1,
            new UnfetchedOptions()
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($series));
    }
}
