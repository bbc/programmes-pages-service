<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use DateTimeImmutable;

class FindUpcomingByProgrammeTest extends AbstractCollapsedBroadcastServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testPagination($expectedLimit, $expectedOffset, array $paginationParams)
    {
        $programme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => [1, 2, 3]]);

        $this->mockRepository->expects($this->once())
            ->method('findUpcomingByProgramme')
            ->with($programme->getDbAncestryIds(), false, $this->lessThanOrEqual(new DateTimeImmutable()), $expectedLimit, $expectedOffset);

        $this->service()->findUpcomingByProgramme($programme, ...$paginationParams);
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
            ->method('findUpcomingByProgramme')
            ->willReturn([
                 ['areWebcasts' => [false, false], 'serviceIds' => [111, 222], 'broadcastIds' => [1,2,3,4]],
                 ['areWebcasts' => [true, true], 'serviceIds' => [333, 444], 'broadcastIds' => [3,4, 56, 67]],
                 ['areWebcasts' => [true, false], 'serviceIds' => [555, 666], 'broadcastIds' => [5,6,100]],
                 ['areWebcasts' => [false, false], 'serviceIds' => [false, false], 'broadcastIds' => [7, 8, 20, 48, 23]],
            ]);

        $this->mockServiceRepository->expects($this->once())
            ->method('findByIds')
            ->with([111, 222, 666]);

        $this->service()->findUpcomingByProgramme($programme);
    }

    public function testCollapsedBroadcastedAreReturnedWithCorrespondingServices()
    {
        $programme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => [997, 998, 999]]);

        $this->mockRepository
            ->method('findUpcomingByProgramme')
            ->willReturn([
                 ['areWebcasts' => [false, false], 'serviceIds' => [111, 222], 'broadcastIds' => [1,2,3,4]],
             ]);

        $this->mockServiceRepository
            ->method('findByIds')
            ->willReturn([['id' => 111, 'sid' => 'bbc_one'], ['id' => 222, 'sid' => 'bbc_one_hd']]);

        $collapsedBroadcasts = $this->service()->findUpcomingByProgramme($programme);

        $this->assertCount(1, $collapsedBroadcasts);
        $this->assertContainsOnly(CollapsedBroadcast::class, $collapsedBroadcasts);

        $servicesInBroadcast = $collapsedBroadcasts[0]->getServices();
        $this->assertCount(2, $servicesInBroadcast);
        $this->assertSame('bbc_one', (string) $servicesInBroadcast[111]->getSid());
        $this->assertSame('bbc_one_hd', (string) $servicesInBroadcast[222]->getSid());
    }

    public function testNoCollpasedBroadcastsFound()
    {
        $this->mockRepository->method('findUpcomingByProgramme')->willReturn([]);

        $this->mockServiceRepository->expects($this->never())
            ->method('findByIds');

        $collapsedBroadcasts = $this->service()->findUpcomingByProgramme($this->createMock(Programme::class));
        $this->assertEquals([], $collapsedBroadcasts);
    }


}
