<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedMasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedOptions;

class CoreEntityMapperMasterBrandMappingTest extends BaseCoreEntityMapperTestCase
{
    /**
     * When the current entity has a MasterBrand and no parent
     *
     * @dataProvider masterBrandOnCurrentEntityDataProvider
     */
    public function testWhenMasterBrandOnCurrentEntityIsSet(array $options, $expectedMasterBrandDomainEntity)
    {
        $masterBrandDbEntity = [
            'mid' => 'bbc_one',
            'network' => null,
        ];

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

        $this->assertEquals($expectedEntity, $this->getMapper($options)->getDomainModelForProgramme($dbEntityArray));
    }

    public function masterBrandOnCurrentEntityDataProvider(): array
    {
        return [
            'Inheritance enabled (by default) - populates MasterBrand from self' => [
                [],
                $this->createMock(MasterBrand::class),
            ],
            'Inheritance enabled - populates MasterBrand from self' => [
                ['core_entity_inherit_master_brand' => false],
                $this->createMock(MasterBrand::class),
            ],
            'Inheritance disabled - populates MasterBrand from self' => [
                ['core_entity_inherit_master_brand' => true],
                $this->createMock(MasterBrand::class),
            ],
        ];
    }

    /**
     * When the current entity does not have a MasterBrand but it has a parent
     * that does have a MasterBrand
     *
     * @dataProvider masterBrandOnParentEntityDataProvider
     */
    public function testWhenMasterBrandOnParentIsSet(array $options, $expectedMasterBrandDomainEntity)
    {
        $masterBrandDbEntity = [
            'mid' => 'bbc_one',
            'network' => null,
        ];

        $this->mockMasterBrandMapper->expects($this->atLeastOnce())
            ->method('getDomainModel')
            ->will($this->returnValueMap([
                [$masterBrandDbEntity, $expectedMasterBrandDomainEntity],
            ]));

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b00swyx1',
            null,
            null,
            [],
            $this->getSampleProgrammeDbEntity(
                'b010t19z',
                null,
                $masterBrandDbEntity
            )
        );

        $expectedEntity = $this->getSampleProgrammeDomainEntity(
            'b00swyx1',
            $this->mockDefaultImage,
            $expectedMasterBrandDomainEntity,
            [],
            [],
            $this->getSampleProgrammeDomainEntity(
                'b010t19z',
                $this->mockDefaultImage,
                $expectedMasterBrandDomainEntity
            )
        );

        $this->assertEquals($expectedEntity, $this->getMapper($options)->getDomainModelForProgramme($dbEntityArray));
    }

    public function masterBrandOnParentEntityDataProvider(): array
    {
        return [
            'Inheritance enabled (by default) - populates MasterBrand from parent' => [
                [],
                $this->createMock(MasterBrand::class),
            ],
            'Inheritance enabled - populates MasterBrand from parent' => [
                ['core_entity_inherit_master_brand' => true],
                $this->createMock(MasterBrand::class),
            ],
            'Inheritance disabled - does not populate MasterBrand from parent' => [
                ['core_entity_inherit_master_brand' => false],
                null,
            ],
        ];
    }

    public function testWhenMasterBrandIsNotSetThenUnfetchedMasterBrandIsUsed()
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
