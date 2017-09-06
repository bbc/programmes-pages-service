<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesAggregationService;

use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class FindDescendantClipsTest extends AbstractProgrammesAggregationTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testProtocolWithRepositoryCollaborator(int $expectedLimit, int $expectedOffset, array $paramsPagination)
    {
        $stubProgramme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => [11, 12]]);

        $this->mockRepository->expects($this->once())
            ->method('findStreamableDescendantsByType')
            ->with($stubProgramme->getDbAncestryIds(), 'Clip', $expectedLimit, $expectedOffset);

        $this->service()->findDescendantClips($stubProgramme, ...$paramsPagination);
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
     * @dataProvider dbClipsProvider
     */
    public function testResultsaaa(array $expectedClipsPids, array $dbClipsProvided)
    {
        $this->mockRepository->method('findStreamableDescendantsByType')->willReturn($dbClipsProvided);

        $stubProgramme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => [11, 12]]);

        $clips = $this->service()->findDescendantClips($stubProgramme, 3, 5);

        $this->assertContainsOnlyInstancesOf(Clip::class, $clips);
        $this->assertCount(count($dbClipsProvided), $clips);
        foreach ($expectedClipsPids as $i => $expectedPid) {
            $this->assertEquals($expectedPid, $clips[$i]->getPid());
        }
    }

    public function dbClipsProvider(): array
    {
        return [
            'CASE: clips results found' => [
                ['p002b7q9', 'p002kzxk'],
                [['type' => 'clip', 'pid' => 'p002b7q9'], ['type' => 'clip', 'pid' => 'p002kzxk']],
            ],
            'CASE: clips results NOT found' => [[], []],
        ];
    }
}
