<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesAggregationService;

use BBC\ProgrammesPagesService\Domain\Entity\Gallery;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class FindDescendantGalleriesTest extends AbstractProgrammesAggregationTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testProtocolWithRepositoryCollaborator(int $expectedLimit, int $expectedOffset, array $paramsPagination)
    {
        $stubProgramme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => [11, 12]]);

        $this->mockRepository->expects($this->once())
            ->method('findDescendantsByType')
            ->with($stubProgramme->getDbAncestryIds(), 'Gallery', $expectedLimit, $expectedOffset);

        $this->service()->findDescendantGalleries($stubProgramme, ...$paramsPagination);
    }

    public function paginationProvider(): array
    {
        return [
            // [expectedLimit, expectedOffset, [limit, page]]
            'default pagination' => [300, 0, []],
            'custom pagination' => [3, 12, [3, 5]],
        ];
    }

    /**
     * @dataProvider dbGalleriesProvider
     */
    public function testFindDescendantGallery(array $expectedPids, array $galleriesResultsProvided)
    {
        $this->mockRepository->method('findDescendantsByType')->willReturn($galleriesResultsProvided);

        $galleries = $this->service()->findDescendantGalleries(
            $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => [11, 12]])
        );

        $this->assertContainsOnlyInstancesOf(Gallery::class, $galleries);
        $this->assertCount(count($galleriesResultsProvided), $galleries);
        foreach ($expectedPids as $i => $expectedPid) {
            $this->assertEquals($expectedPid, $galleries[$i]->getPid());
        }
    }

    public function dbGalleriesProvider(): array
    {
        return [
            'CASE: galleries results found' => [
                ['p00m16sh', 'p00m172y'],
                [['pid' => 'p00m16sh', 'type' => 'gallery'], ['pid' => 'p00m172y', 'type' => 'gallery']],
            ],
            'CASE: galleries results not found' => [
                [],
                [],
            ],
        ];
    }
}
