<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesAggregationService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class CountUpcomingStreamableDescendantEpisodesTest extends AbstractProgrammesAggregationTest
{
    /**
     * @dataProvider dbEpisodesProvider
     */
    public function testResults(int $episodeCount, array $dbEpisodesProvided)
    {
        $this->mockRepository->method('findUpcomingStreamableDescendantsByType')->willReturn($dbEpisodesProvided);
        $programmeMock = $this->createMock(Programme::class);
        $this->assertSame($episodeCount, $this->service()->countUpcomingStreamableDescendantEpisodes($programmeMock));
    }

    public function dbEpisodesProvider(): array
    {
        return [
            'CASE: episodes results found' => [
                2,
                [['type' => 'episode', 'pid' => 'p002b7q9'], ['type' => 'episode', 'pid' => 'p002kzxk']],
            ],
            'CASE: episodes results NOT found' => [0, []],
        ];
    }
}
