<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper;

class ProgrammeMapperImageMappingTest extends BaseProgrammeMapperTestCase
{

    public function testWhenImageIsNotSetThenTheDefaultImageIsUsed()
    {
        $imageDbEntity = ['pid' => 'p01m5mss'];

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b010t19z',
            null,
            null
        );

        $domainModel = $this->getMapper()->getDomainModel($dbEntityArray);
        $this->assertEquals('DefaultImage', $domainModel->getImage()->getTitle());
    }
    public function testWhenImageIsSetThenTheSetImageIsUsed()
    {
        $imageDbEntity = ['pid' => 'p01m5mss'];

        $expectedImageDomainEntity = $this->getMockWithoutInvokingTheOriginalConstructor(
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

        $domainModel = $this->getMapper()->getDomainModel($dbEntityArray);
        $this->assertEquals('SetImage', $domainModel->getImage()->getTitle());
    }

    public function testWhenImageOnParentIsSetThenTheInheritedImageIsUsed()
    {
        $imageDbEntity = ['pid' => 'p01m5mss'];

        $expectedImageDomainEntity = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Image'
        );

        $expectedImageDomainEntity->method('getTitle')->willReturn('InheritedImage');

        $this->mockImageMapper->expects($this->exactly(2))
            ->method('getDomainModel')
            ->with($imageDbEntity)
            ->willReturn($expectedImageDomainEntity);

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b00swyx1',
            null,
            null,
            [],
            $this->getSampleProgrammeDbEntity(
                'b010t19z',
                $imageDbEntity
            )
        );

        $domainModel = $this->getMapper()->getDomainModel($dbEntityArray);
        $this->assertEquals('InheritedImage', $domainModel->getImage()->getTitle());
        $this->assertEquals('InheritedImage', $domainModel->getParent()->getImage()->getTitle());
    }
}
