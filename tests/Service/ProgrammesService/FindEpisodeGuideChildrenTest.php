<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class FindEpisodeGuideChildrenTest extends AbstractProgrammesServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testProtocolWithRepository(int $expectedLimit, int $expectedOffset, array $paramsPagination)
    {
        $programme = $this->createConfiguredMock(Programme::class, ['getDbId' => 1]);

        $this->mockRepository->expects($this->once())
            ->method('findEpisodeGuideChildren')
            ->with($programme->getDbId(), $expectedLimit, $expectedOffset);

        $this->service()->findEpisodeGuideChildren($programme, ...$paramsPagination);
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
    public function testFindEpisodeGuideChildrenWithNonExistantPid(array $expectedPids, array $dbEpisodesProvided)
    {
        $this->mockRepository
            ->method('findEpisodeGuideChildren')
            ->willReturn($dbEpisodesProvided);

        $episodes = $this->service()->findEpisodeGuideChildren($this->createMock(Programme::class));

        $this->assertCount(count($dbEpisodesProvided), $episodes);
        $this->assertContainsOnlyInstancesOf(Programme::class, $episodes);
        foreach ($expectedPids as $i => $expectedPid) {
            $this->assertEquals($expectedPid, $episodes[$i]->getPid());
        }
    }

    public function dbEpisodesProvider(): array
    {
        return [
            'CASE: results episodes found' => [
                ['b010t19z', 'b00swyx1'],
                [['pid' => 'b010t19z'], ['pid' => 'b00swyx1']],
            ],
            'CASE: results episodes NOT found' => [
                [],
                [],
            ],
        ];
    }
}
