<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use DateTimeImmutable;

class FindUpcomingByServiceTest extends AbstractBroadcastsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testFindUpcomingByServicePagination($expectedLimit, $expectedOffset, $paginationParams)
    {
        $dummyService = $this->createConfiguredMock(Service::class, ['getDbId' => 1]);

        $this->mockRepository->expects($this->once())->method('findUpcomingByService')
             ->with(1, 'Broadcast', $this->isInstanceOf(DateTimeImmutable::class), $expectedLimit, $expectedOffset);

        $this->service()->findUpcomingByService($dummyService, ...$paginationParams);
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
     * @dataProvider repositoryResultsProvider
     */
    public function testFindUpcomingByServiceResults($expectedPids, $stubRepositoryResults)
    {
        $this->mockRepository->method('findUpcomingByService')->willReturn($stubRepositoryResults);

        $broadcasts = $this->service()->findUpcomingByService(
            $this->createMock(Service::class)
        );

        $this->assertCount(count($stubRepositoryResults), $broadcasts);
        $this->assertContainsOnly(Broadcast::class, $broadcasts);
        foreach ($expectedPids as $i => $expectedPid) {
            $this->assertEquals($expectedPid, $broadcasts[$i]->getPid());
        }
    }

    public function repositoryResultsProvider(): array
    {
        return [
            // [expectations], [results]
            'broadcasts results' => [['b00swyx1', 'b010t150'], [['pid' => 'b00swyx1'], ['pid' => 'b010t150']]],
            'no results' => [[], []],
        ];
    }
}
