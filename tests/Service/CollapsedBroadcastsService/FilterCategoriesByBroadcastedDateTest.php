<?php
namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use DateTimeImmutable;

class FilterCategoriesByBroadcastedDateTest extends AbstractCollapsedBroadcastServiceTest
{
    public function testFilterCategoriesByBroadcastedDate()
    {
        $dbAncestry1 = [1, 2, 3];
        $category1 = $this->mockEntity('Genre', 3);
        $category1->method('getDbAncestryIds')->willReturn($dbAncestry1);

        $dbAncestry2 = [5, 6, 7];
        $category2 = $this->mockEntity('Genre', 5);
        $category2->method('getDbAncestryIds')->willReturn($dbAncestry2);

        $mockBroadcastedResults = [
            ['ancestry' => '1,2,3,'],
            ['ancestry' => '1,2,3,4,'],
        ];

        $start = new DateTimeImmutable();
        $end = new DateTimeImmutable();

        $this->mockRepository->expects($this->once())
            ->method('filterCategoriesByBroadcastedDates')
            ->with([$dbAncestry1, $dbAncestry2], false, $start, $end)
            ->willReturn($mockBroadcastedResults);

        $dbBroadcastedCategories = $this->service()->filterCategoriesByBroadcastedDate(
            [$category1, $category2],
            $start,
            $end
        );

        $this->assertCount(1, $dbBroadcastedCategories);
        $this->assertSame([1, 2, 3], $dbBroadcastedCategories[0]->getDbAncestryIds());
    }
}
