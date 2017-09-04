<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use DateTimeImmutable;

class FindByCategoryAndEndAtDateRangeTest extends AbstractCollapsedBroadcastServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testRepositoryReceivesCorrectParams($expectedLimit, $expectedOffset, array $paginationParams)
    {
        $fromDate = new DateTimeImmutable();
        $toDate = new DateTimeImmutable();

        $stubCategory = $this->createConfiguredMock(Genre::class, ['getDbAncestryIds' => [3]]);

        $this->mockRepository->expects($this->once())
            ->method('findByCategoryAncestryAndEndAtDateRange')
            ->with($stubCategory->getDbAncestryIds(), false, $fromDate, $toDate, $expectedLimit, $expectedOffset);

        $this->service()->findByCategoryAndEndAtDateRange($stubCategory, $fromDate, $toDate, ...$paginationParams);
    }

    public function paginationProvider(): array
    {
        return [
            // [expectedLimit, expectedOffset, [limit, page]]
            'CASE: default pagination' => [300, 0, []],
            'CASE: custom pagination' => [3, 12, [3, 5]],
        ];
    }

    public function testWebcastIsStripped()
    {
        $this->mockRepository
            ->method('findByCategoryAncestryAndEndAtDateRange')
            ->willReturn([
                 ['areWebcasts' => [false, false], 'serviceIds' => [111, 222], 'broadcastIds' => [1,2,3,4]],
                 ['areWebcasts' => [true, true], 'serviceIds' => [333, 444], 'broadcastIds' => [3,4, 56, 67]],
                 ['areWebcasts' => [true, false], 'serviceIds' => [555, 666], 'broadcastIds' => [5,6,100]],
                 ['areWebcasts' => [false, false], 'serviceIds' => [false, false], 'broadcastIds' => [7, 8, 20, 48, 23]],
                 ['areWebcasts' => [false, false], 'serviceIds' => [true, true], 'broadcastIds' => [8, 9, 12, 122]],
                 ['areWebcasts' => [false, false], 'serviceIds' => [null, null], 'broadcastIds' => [10, 11]],
                 ['areWebcasts' => ['0', 0], 'serviceIds' => [777, 888], 'broadcastIds' => [14, 15]],
                 ['areWebcasts' => [1, '1'], 'serviceIds' => [999, 1010], 'broadcastIds' => [16, 17]],
            ]);

        // fetch services for broadcasts no webcasted
        $this->mockServiceRepository->expects($this->once())
            ->method('findByIds')->with([111, 222, 666, 777, 888]);

        $this->service()->findByCategoryAndEndAtDateRange(
            $this->createMock(Genre::class),
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );
    }

    public function testFindByCategoryAndEndAtDateRangeResults()
    {
        $stubCategory = $this->createConfiguredMock(Genre::class, ['getDbId' => 3, 'getDbAncestryIds' => [3]]);

        $this->mockRepository
            ->method('findByCategoryAncestryAndEndAtDateRange')
            ->willReturn([
                ['areWebcasts' => [false, false, true], 'serviceIds' => [111, 222, 333], 'broadcastIds' => [1,2, 3]],
            ]);

        $this->mockServiceRepository
            ->method('findByIds')
            ->willReturn([['id' => 111, 'sid' => 'bbc_one'], ['id' => 222, 'sid' => 'bbc_one_hd']]);

        $collapsedBroadcasts = $this->service()->findByCategoryAndEndAtDateRange($stubCategory, new DateTimeImmutable(), new DateTimeImmutable());

        $this->assertCount(1, $collapsedBroadcasts);
        $this->assertContainsOnly(CollapsedBroadcast::class, $collapsedBroadcasts);

        $servicesInBroadcast = $collapsedBroadcasts[0]->getServices();
        $this->assertCount(2, $servicesInBroadcast);
        $this->assertSame('bbc_one', (string) $servicesInBroadcast[111]->getSid());
        $this->assertSame('bbc_one_hd', (string) $servicesInBroadcast[222]->getSid());
    }

    public function testResultIsEmptyWhenTheSpecifiedCategoryHasNotBeenBroadcastedOnThatPerio()
    {
        $this->mockRepository->method('findByCategoryAncestryAndEndAtDateRange')->willReturn([]);

        $this->mockServiceRepository->expects($this->never())->method('findByIds');

        $this->service()->findByCategoryAndEndAtDateRange(
            $this->createMock(Genre::class),
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );
    }
}
