<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

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
}
