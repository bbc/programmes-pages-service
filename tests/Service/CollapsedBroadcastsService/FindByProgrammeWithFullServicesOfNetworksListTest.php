<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use DateTimeImmutable;

class FindByProgrammeWithFullServicesOfNetworksListTest extends AbstractCollapsedBroadcastServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testPaginationDev($expectedLimit, $expectedOffset, array $paginationParams)
    {
        $programme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => [1, 2, 3]]);

        $this->mockRepository->expects($this->once())
            ->method('findByProgramme')
            ->with($programme->getDbId(), false, $expectedLimit, $expectedOffset);

        $this->service()->findByProgrammeWithFullServicesOfNetworksList($programme, ...$paginationParams);
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
        $stubProgramme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => [1, 2, 3]]);
        $startAt = new DateTimeImmutable();

        $this->mockRepository
            ->method('findByProgramme')
            ->willReturn([
                 ['areWebcasts' => [0, '0'], 'serviceIds' => [111, 222], 'broadcastIds' => [1, 2, 3, 4], 'startAt' => $startAt],
                 ['areWebcasts' => [1, '1'], 'serviceIds' => [333, 444], 'broadcastIds' => [3, 4, 56, 67], 'startAt' => $startAt],
                 ['areWebcasts' => [1, 0], 'serviceIds' => [555, 666], 'broadcastIds' => [5, 6, 100], 'startAt' => $startAt],
             ]);

        $this->mockServiceRepository->expects($this->once())
            ->method('findByIdsWithNetworkServicesList')
            ->with([111, 222, 666], $startAt);

        $this->service()->findByProgrammeWithFullServicesOfNetworksList($stubProgramme);
    }

    public function testFindByProgrammeWithExistantPid()
    {
        $stubProgramme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => [997, 998, 999]]);
        $startAt = new DateTimeImmutable();

        $this->mockRepository
            ->method('findByProgramme')
            ->willReturn([
                 ['areWebcasts' => [false, false], 'serviceIds' => [111, 222], 'broadcastIds' => [1, 2, 3, 4], 'startAt' => $startAt],
             ]);

        $this->mockServiceRepository
            ->method('findByIdsWithNetworkServicesList')
            ->willReturn([['id' => 111, 'sid' => 'bbc_one'], ['id' => 222, 'sid' => 'bbc_one_hd']]);

        $collapsedBroadcasts = $this->service()->findByProgrammeWithFullServicesOfNetworksList($stubProgramme);

        $this->assertCount(1, $collapsedBroadcasts);
        $this->assertContainsOnly(CollapsedBroadcast::class, $collapsedBroadcasts);

        $servicesInBroadcast = $collapsedBroadcasts[0]->getServices();
        $this->assertCount(2, $servicesInBroadcast);
        $this->assertSame('bbc_one', (string) $servicesInBroadcast[111]->getSid());
        $this->assertSame('bbc_one_hd', (string) $servicesInBroadcast[222]->getSid());
    }

    public function testFindByProgrammeWithNonExistantPid()
    {
        $this->mockRepository->method('findByProgramme')->willReturn([]);

        $this->mockServiceRepository->expects($this->never())->method('findByIds');

        $this->assertEquals(
            [],
            $this->service()->findByProgrammeWithFullServicesOfNetworksList($this->createMock(Programme::class))
        );
    }
}
