<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesAggregationService;

use BBC\ProgrammesPagesService\Domain\ApplicationTime;
use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class FindStreamableDescendantEpisodesTest extends AbstractProgrammesAggregationTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testProtocolWithRepositoryCollaborator(int $expectedLimit, int $expectedOffset, array $paramsPagination)
    {
        $stubProgramme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => [11, 12]]);

        $this->mockRepository->expects($this->once())
            ->method('findStreamableDescendantsByType')
            ->with($stubProgramme->getDbAncestryIds(), 'Episode', ApplicationTime::getTime(), $expectedLimit, $expectedOffset);

        $this->service()->findStreamableDescendantEpisodes($stubProgramme, ...$paramsPagination);
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
     * @dataProvider dbEpisodesProvider
     */
    public function testResults(array $expectedEpisodesPids, array $dbEpisodesProvided)
    {
        $this->mockRepository->method('findStreamableDescendantsByType')->willReturn($dbEpisodesProvided);

        $episodes = $this->service()->findStreamableDescendantEpisodes($this->createMock(Programme::class));

        $this->assertContainsOnlyInstancesOf(Episode::class, $episodes);
        $this->assertCount(count($dbEpisodesProvided), $episodes);
        foreach ($expectedEpisodesPids as $i => $expectedPid) {
            $this->assertEquals($expectedPid, $episodes[$i]->getPid());
        }
    }

    public function dbEpisodesProvider(): array
    {
        return [
            'CASE: episodes results found' => [
                ['p002b7q9', 'p002kzxk'],
                [['type' => 'episode', 'pid' => 'p002b7q9'], ['type' => 'episode', 'pid' => 'p002kzxk']],
            ],
            'CASE: episodes results NOT found' => [[], []],
        ];
    }
}
