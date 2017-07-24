<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

class CoreEntityMapperImageMappingTest extends BaseProgrammeMapperTestCase
{

    public function testWhenImageIsNotSetThenTheDefaultImageIsUsed()
    {
        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b010t19z',
            null,
            null
        );

        $domainModel = $this->getMapper()->getDomainModelForProgramme($dbEntityArray);
        $this->assertEquals('DefaultImage', $domainModel->getImage()->getTitle());
    }

    public function testWhenImageIsSetThenTheSetImageIsUsed()
    {
        $imageDbEntity = ['pid' => 'p01m5mss'];

        $expectedImageDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Image'
        );

        $expectedImageDomainEntity->method('getTitle')->willReturn('SetImage');

        $this->mockImageMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($imageDbEntity)
            ->willReturn($expectedImageDomainEntity);

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b010t19z',
            $imageDbEntity,
            null
        );

        $domainModel = $this->getMapper()->getDomainModelForProgramme($dbEntityArray);
        $this->assertEquals('SetImage', $domainModel->getImage()->getTitle());
    }

    public function testWhenImageOnParentIsSetThenTheInheritedImageIsUsed()
    {
        $imageDbEntity = ['pid' => 'p01m5mss'];

        $expectedImageDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Image'
        );

        $expectedImageDomainEntity->method('getTitle')->willReturn('InheritedImage');

        $this->mockImageMapper->expects($this->atLeastOnce())
            ->method('getDomainModel')
            ->will($this->returnValueMap([
                [$imageDbEntity, $expectedImageDomainEntity],
            ]));

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b00swyx1',
            null,
            ['mid' => 'bbc_one', 'image' => ['pid' => 'zzzzzzzz']],
            [],
            $this->getSampleProgrammeDbEntity(
                'b010t19z',
                $imageDbEntity
            )
        );

        $domainModel = $this->getMapper()->getDomainModelForProgramme($dbEntityArray);
        $this->assertEquals('InheritedImage', $domainModel->getImage()->getTitle());
        $this->assertEquals('InheritedImage', $domainModel->getParent()->getImage()->getTitle());
    }

    public function testWhenImageOnMasterBrandIsSetThenTheMasterBrandImageIsUsed()
    {
        $imageDbEntity = ['pid' => 'p01m5mss'];

        $expectedImageDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Image'
        );

        $expectedImageDomainEntity->method('getTitle')->willReturn('MasterBrandImage');

        $this->mockImageMapper->expects($this->atLeastOnce())
            ->method('getDomainModel')
            ->will($this->returnValueMap([
                [$imageDbEntity, $expectedImageDomainEntity],
            ]));

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b00swyx1',
            null,
            ['mid' => 'bbc_one', 'image' => $imageDbEntity],
            [],
            null
        );

        $domainModel = $this->getMapper()->getDomainModelForProgramme($dbEntityArray);
        $this->assertEquals('MasterBrandImage', $domainModel->getImage()->getTitle());
    }

    public function testWhenImageOnParentsMasterBrandIsSetThenTheParentsMasterBrandImageIsUsed()
    {
        $imageDbEntity = ['pid' => 'p01m5mss'];

        $expectedImageDomainEntity = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Image'
        );

        $expectedImageDomainEntity->method('getTitle')->willReturn('InheritedMasterBrandImage');

        $this->mockImageMapper->expects($this->atLeastOnce())
            ->method('getDomainModel')
            ->will($this->returnValueMap([
                [$imageDbEntity, $expectedImageDomainEntity],
            ]));

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b00swyx1',
            null,
            null,
            [],
            $this->getSampleProgrammeDbEntity(
                'b010t19z',
                null,
                ['mid' => 'bbc_one', 'image' => $imageDbEntity]
            )
        );

        $domainModel = $this->getMapper()->getDomainModelForProgramme($dbEntityArray);
        $this->assertEquals('InheritedMasterBrandImage', $domainModel->getImage()->getTitle());
        $this->assertEquals('InheritedMasterBrandImage', $domainModel->getParent()->getImage()->getTitle());
    }
}
