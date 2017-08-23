<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Version;

class FindByVersionTest extends AbstractBroadcastsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testFindByServiceAndDateRangePagination($expectedLimit, $expectedOffset, $paginationParams)
    {
        $dbId = 1;
        $stubVersion = $this->createMock(Version::class);
        $stubVersion->method('getDbId')->willReturn($dbId);

        $this->mockRepository
            ->expects($this->once())
            ->method('findByVersion')
            ->with([$dbId], 'Broadcast', $expectedLimit, $expectedOffset);

        $this->service()->findByVersion($stubVersion, ...$paginationParams);
    }

    public function paginationProvider(): array
    {
        return [
            // [expectedLimit, expectedOffset, [limit, page]]
            'default pagination' => [300, 0, []],
            'custom pagination' => [5, 10, [5, 3]],
        ];
    }

    /**
     * @dataProvider repositoryResultsProvider
     */
    public function testFindByVersionWithExistantDbId($expectedPids, $stubBroadcasts)
    {
        $this->mockRepository
            ->method('findByVersion')
            ->willReturn($stubBroadcasts);

        $dummyVersion = $this->createMock(Version::class);
        $stubBroadcasts = $this->service()->findByVersion($dummyVersion);

        $this->assertCount(count($expectedPids), $stubBroadcasts);
        $this->assertContainsOnly(Broadcast::class, $stubBroadcasts);
        $this->assertSame($expectedPids, $this->extractPids($stubBroadcasts));
    }

    public function repositoryResultsProvider(): array
    {

        return [
            [
                ['b00swyx1', 'b010t150'],
                [['pid' => 'b00swyx1'], ['pid' => 'b010t150']],
            ],
            [
                [],
                [],
            ],
        ];
    }

    /**
     * @param Broadcast[] $broadcasts
     * @return string[]
     */
    private function extractPids(array $broadcasts): array
    {
        return array_map(
            function ($broadcast) {
                return (string) $broadcast->getPid();
            },
            $broadcasts
        );
    }
}
