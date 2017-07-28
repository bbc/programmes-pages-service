<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesAggregationService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use InvalidArgumentException;

/**
 * @group service
 * @group programmesAggregation
 */
class FindChildrenByTypeTest extends AbstractProgrammesAggregationTest
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testCannotGetBrandTypesChildrenForAnyAncestry()
    {
        $mockProgramme = $this->createMock(Programme::class);
        $mockProgramme->method('getDbAncestryIds')->willReturn([11, 12]);
        $this->service()->findChildrenByType($mockProgramme, 'Brand');
    }

    public function testFindChildrenByTypeCallsProperMethods()
    {
        $mockProgramme = $this->createMock(Programme::class);
        $mockProgramme->method('getDbAncestryIds')->willReturn([11, 12]);

        $this->mockRepository->expects($this->once())
                             ->method('findProgrammesByAncestryAndType')
                             ->willReturn([['type' => 'clip'], ['type' => 'clip']]);


        $mappedResults = $this->service()->findChildrenByType($mockProgramme, 'Clip');
        $this->assertContainsOnlyInstancesOf('BBC\ProgrammesPagesService\Domain\Entity\Clip', $mappedResults);
        $this->assertCount(2, $mappedResults);
    }
}
