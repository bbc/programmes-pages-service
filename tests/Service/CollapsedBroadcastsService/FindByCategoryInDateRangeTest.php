<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use DateTimeImmutable;

class FindByCategoryInDateRangeTest extends AbstractCollapsedBroadcastServiceTest
{
    public function testFindByCategoryInDateRange()
    {
        $ancestry = [3];
        $fromDate = new DateTimeImmutable();
        $toDate = new DateTimeImmutable();

        $category = $this->mockEntity('Genre', 3);
        $category->method('getDbAncestryIds')->willReturn($ancestry);

        $broadcastData = [['serviceIds' => ['a', 'b']]];
        $serviceData   = [
            'a' => ['sid' => 'bbc_one'],
            'b' => ['sid' => 'bbc_one_hd'],
        ];

        $this->mockRepository->expects($this->once())
            ->method('findByCategoryAncestryInDateRange')
            ->with($ancestry, 'Broadcast', null, $fromDate, $toDate)
            ->willReturn($broadcastData);

        $this->mockServiceRepository->expects($this->once())
            ->method('findBySids')
            ->with(['a', 'b'])
            ->willReturn($serviceData);

        $result = $this->service()->findByCategoryInDateRange($category, $fromDate, $toDate);
        $this->assertEquals($this->collapsedBroadcastsFromDbData($broadcastData), $result);
    }
}
