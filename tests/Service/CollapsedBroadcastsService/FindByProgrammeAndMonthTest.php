<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class FindByProgrammeAndMonthTest extends AbstractCollapsedBroadcastServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testPagination(int $expectedLimit, int $expectedOffset, array $paginationParams)
    {
        $dbAncestry = [1, 2, 3];
        $programme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => $dbAncestry]);

        $this->mockRepository->expects($this->once())
            ->method('findByProgrammeAndMonth')
            ->with($dbAncestry, false, 2007, 12, $expectedLimit, $expectedOffset);

        $this->service()->findByProgrammeAndMonth($programme, 2007, 12, ...$paginationParams);
    }

    public function paginationProvider(): array
    {
        return [
            // expectedLimit, expectedOffset, [limit, page]
            'CASE: default pagination' => [300, 0, []],
            'CASE: custom pagination' => [5, 10, [5, 3]],
        ];
    }

    public function testWebcastIsStripped()
    {
        $programme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => [1, 2, 3]]);

        $this->mockRepository
            ->method('findByProgrammeAndMonth')
            ->willReturn([
                 ['areWebcasts' => [0, '0'], 'serviceIds' => [111, 222], 'broadcastIds' => [1, 2, 3, 4]],
                 ['areWebcasts' => [1, '1'], 'serviceIds' => [333, 444], 'broadcastIds' => [3, 4, 56, 67]],
                 ['areWebcasts' => [1, 0], 'serviceIds' => [555, 666], 'broadcastIds' => [5, 6, 100]],
            ]);

        $this->mockServiceRepository->expects($this->once())
            ->method('findByIds')
            ->with([111, 222, 666]);

        $this->service()->findByProgrammeAndMonth($programme, 2007, 12);
    }

    public function testCollapsedBroadcastsEntitiesAreReturnedWithRespectiveServices()
    {
        $stubProgramme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => [1, 2, 3]]);

        $this->mockRepository
            ->method('findByProgrammeAndMonth')
            ->willReturn([
                 ['areWebcasts' => [false, false, true], 'serviceIds' => [111, 222, 333], 'broadcastIds' => [1, 2, 3]],
             ]);

        $this->mockServiceRepository
            ->method('findByIds')
            ->willReturn([['id' => 111, 'sid' => 'bbc_one'], ['id' => 222, 'sid' => 'bbc_one_hd']]);

        $collapsedBroadcasts = $this->service()->findByProgrammeAndMonth($stubProgramme, 2007, 12);

        $this->assertCount(1, $collapsedBroadcasts);
        $this->assertContainsOnly(CollapsedBroadcast::class, $collapsedBroadcasts);

        $servicesInBroadcast = $collapsedBroadcasts[0]->getServices();
        $this->assertCount(2, $servicesInBroadcast);
        $this->assertSame('bbc_one', (string) $servicesInBroadcast[111]->getSid());
        $this->assertSame('bbc_one_hd', (string) $servicesInBroadcast[222]->getSid());
    }

    public function testResultIsEmptyWhenTheSpecifiedCategoryHasNotBeenBroadcastedOnThatPerio()
    {
        $this->mockRepository->method('findByProgrammeAndMonth')->willReturn([]);

        $this->mockServiceRepository->expects($this->never())->method('findByIds');

        $this->assertEquals(
            [],
            $this->service()->findByProgrammeAndMonth($this->createMock(Programme::class), 2007, 12)
        );
    }
}
