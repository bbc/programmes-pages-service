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

        $this->mockRepository->expects($this->once())
             ->method('findAllByServiceAndDateRange')
             ->with(
                 $dummySid,
                 $dummyFromDate,
                 $dummyToDate,
                 $expectedLimit,
                 $expectedOffset
             );

        $this->service()->findByServiceAndDateRange($dummySid, $dummyFromDate, $dummyToDate, ...$paginationParams);
    }

    public function paginationProvider()
    {
        return [
            'default pagination' => [300, 0, []],
            'custom pagination' => [3, 12, [3, 5]],
        ];
    }

    public function testFindByServiceAndDateRange()
    {
        $this->mockRepository->expects($this->once())
            ->method('findAllByServiceAndDateRange')
            ->willReturn([['pid' => 'b00swyx1'], ['pid' => 'b010t150']]);

        $broadcasts = $this->service()->findByServiceAndDateRange(
            $this->createMock(Sid::class),
            $this->createMock(DateTimeImmutable::class),
            $this->createMock(DateTimeImmutable::class)
        );

        $this->assertCount(2, $broadcasts);
        $this->assertContainsOnly(Broadcast::class, $broadcasts);
        $this->assertEquals('b00swyx1', (string) $broadcasts[0]->getPid());
        $this->assertEquals('b010t150', (string) $broadcasts[1]->getPid());
    }

    public function testFindByServiceAndDateRangeWhenNoBroadcastsFound()
    {
        $this->mockRepository->expects($this->once())
            ->method('findAllByServiceAndDateRange')
            ->willReturn([]);

        $broadcasts = $this->service()->findByServiceAndDateRange(
            $this->createMock(Sid::class),
            $this->createMock(DateTimeImmutable::class),
            $this->createMock(DateTimeImmutable::class)
        );

        $this->assertSame([], $broadcasts);
    }
}
