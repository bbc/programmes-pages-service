<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTimeImmutable;

class FindByServiceAndDateRangeTest extends AbstractBroadcastsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testFindByServiceAndDateRangePagination($expectedLimit, $expectedOffset, $paginationParams)
    {
        $dummyFromDate = $this->createMock(DateTimeImmutable::class);
        $dummyToDate = $this->createMock(DateTimeImmutable::class);
        $dummySid = $this->createMock(Sid::class);

        $this->mockRepository->expects($this->once())->method('findAllByServiceAndDateRange')
             ->with($dummySid, $dummyFromDate, $dummyToDate, $expectedLimit, $expectedOffset);

        $this->service()->findByServiceAndDateRange($dummySid, $dummyFromDate, $dummyToDate, ...$paginationParams);
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
    public function testFindByServiceAndDateRangeResults($expectedPids, $stubRepositoryResults)
    {
        $this->mockRepository->method('findAllByServiceAndDateRange')->willReturn($stubRepositoryResults);

        $broadcasts = $this->service()->findByServiceAndDateRange(
            $this->createMock(Sid::class),
            $this->createMock(DateTimeImmutable::class),
            $this->createMock(DateTimeImmutable::class)
        );

        $this->assertContainsOnly(Broadcast::class, $broadcasts);
        $this->assertSame($expectedPids, $this->extractPids($broadcasts));
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
