<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesAggregationService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;

/**
 * @covers BBC\ProgrammesPagesService\Service\ProgrammesAggregationService::findDescendantGalleries
 */
class FindDescendantGalleriesTest extends AbstractProgrammesAggregationTest
{
    public function testFindDescendantGallery()
    {
        $dbAncestryIds = [11, 12];
        $dbData = [
            ['pid' => 'p00m16sh', 'type' => 'gallery'],
            ['pid' => 'p00m172y', 'type' => 'gallery'],
        ];
        $this->mockRepository->expects($this->once())
            ->method('findDescendantsByType')
            ->with($dbAncestryIds, 'Gallery', 300, 0)
            ->willReturn($dbData);

        $mockProgramme = $this->createMock(Programme::class);
        $mockProgramme->method('getDbAncestryIds')->willReturn($dbAncestryIds);
        $mappedResults = $this->service()->findDescendantGalleries($mockProgramme);

        $this->assertEquals($this->programmesFromDbData($dbData), $mappedResults);
        $this->assertContainsOnlyInstancesOf('BBC\ProgrammesPagesService\Domain\Entity\Gallery', $mappedResults);
        $this->assertCount(2, $mappedResults);
    }
}
