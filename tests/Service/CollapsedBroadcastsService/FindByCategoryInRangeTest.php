<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;


use DateTimeImmutable;

class FindByCategoryInRangeTest extends AbstractCollapsedBroadcastServiceTest
{

    public function testFindByCategoryInRange()
    {
        $categoryId    = 3;
        $fromDate      = new DateTimeImmutable();
        $toDate        = new DateTimeImmutable();


        $category = $this->mockEntity('Genre', 3);
        $category->method('getDbId')->willReturn(3);

        $broadcastData = [['serviceIds' => ['a', 'b']]];
        $serviceData   = [
            'a' => ['sid' => 'bbc_one'],
            'b' => ['sid' => 'bbc_one_hd'],
        ];

        $this->mockRepository->expects($this->once())
                             ->method('findByCategoryIdInDateRange')
                             ->with($categoryId, 'Broadcast', $fromDate, $toDate)
                             ->willReturn($broadcastData);

        $this->mockServiceRepository->expects($this->once())
                                    ->method('findBySids')
                                    ->with(['a', 'b'])
                                    ->willReturn($serviceData);

        $result = $this->service()->findByCategoryInRange($category,$fromDate, $toDate);
        $this->assertEquals($this->collapsedBroadcastsFromDbData($broadcastData), $result);
    }
}
