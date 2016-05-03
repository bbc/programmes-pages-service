<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper;

class ProgrammeMapperRelatedLinksMappingTest extends BaseProgrammeMapperTestCase
{
    public function testGetDomainModelSeriesWithSetRelatedLinks()
    {
        $relatedLinkDbEntity = ['pid' => 'p03sm1rm'];

        $expectedRelatedLinkDomainEntity = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\RelatedLink'
        );

        $this->mockRelatedLinkMapper->expects($this->once())
            ->method('getDomainModel')
            ->with($relatedLinkDbEntity)
            ->willReturn($expectedRelatedLinkDomainEntity);

        $dbEntityArray = $this->getSampleProgrammeDbEntity(
            'b010t19z',
            null,
            null,
            [],
            [$relatedLinkDbEntity]
        );

        $expectedEntity = $this->getSampleProgrammeDomainEntity(
            'b010t19z',
            $this->mockDefaultImage,
            null,
            [],
            [],
            [$expectedRelatedLinkDomainEntity]
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }
}
