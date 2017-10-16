<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesAggregationService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class CountUpcomingStreamableDescendantEpisodesTest extends AbstractProgrammesAggregationTest
{
    /**
     * @dataProvider dbEpisodesProvider
     */
    public function testResults(int $episodeCount)
    {
        $this->mockRepository->method('countUpcomingStreamableDescendantsByType')->willReturn($episodeCount);
        $episodes = $this->service()->countUpcomingStreamableDescendantEpisodes($this->createMock(Programme::class));
        $this->assertSame($episodeCount, $episodeCount);
    }

    public function dbEpisodesProvider(): array
    {
        return [
            'CASE: episodes results found' => [2],
            'CASE: episodes results NOT found' => [0],
        ];
    }
}
