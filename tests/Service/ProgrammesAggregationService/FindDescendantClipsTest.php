<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesAggregationService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;

/**
 * @covers BBC\ProgrammesPagesService\Service\ProgrammesAggregationService::findDescendantClips
 */
class FindDescendantClipsTest extends AbstractProgrammesAggregationTest
{
    public function testFindDescendantClips()
    {
        $dbAncestryIds = [11, 12];
        $dbData = [
            ['type' => 'clip', 'pid' => 'p002b7q9'],
            ['type' => 'clip', 'pid' => 'p002kzxk'],
        ];
        $this->mockRepository->expects($this->once())
             ->method('findStreamableDescendantsByType')
             ->with($dbAncestryIds, 'Clip', 3, 12)
             ->willReturn($dbData);

        $mockProgramme = $this->createMock(Programme::class);
        $mockProgramme->method('getDbAncestryIds')->willReturn($dbAncestryIds);
        $mappedResults = $this->service()->findDescendantClips($mockProgramme, 3, 5);

        $this->assertEquals($this->programmesFromDbData($dbData), $mappedResults);
        $this->assertContainsOnlyInstancesOf('BBC\ProgrammesPagesService\Domain\Entity\Clip', $mappedResults);
        $this->assertCount(2, $mappedResults);
    }
}
