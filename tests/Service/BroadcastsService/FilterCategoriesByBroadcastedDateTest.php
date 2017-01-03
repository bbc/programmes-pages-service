<?php
namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use DateTimeImmutable;

Class FilterCategoriesByBroadcastedDateTest extends AbstractBroadcastsServiceTest
{
    public function testFilterCategoriesByBroadcastedDate()
    {
        $dbAncestry1 = [1, 2, 3];
        $category1 = $this->mockEntity('Genre', 3);
        $category1->method('getDbAncestryIds')->willReturn($dbAncestry1);

        $dbAncestry2 = [5, 6, 7];
        $category2 = $this->mockEntity('Genre', 5);
        $category2->method('getDbAncestryIds')->willReturn($dbAncestry2);

        $dbBroadcastedResults = [
            ['ancestry' => '1,2,3,', 'day' => '1', 'month' => '8', 'year' => '2011'],
            ['ancestry' => '1,2,3,4,', 'day' => '2', 'month' => '8', 'year' => '2011']
        ];

        $start = new DateTimeImmutable();
        $end = new DateTimeImmutable();

        $this->mockRepository->expects($this->once())
                             ->method('findBroadcastedDatesForCategories')
                             ->with([$dbAncestry1, $dbAncestry2], 'Broadcast', null, $start, $end)
                             ->willReturn($dbBroadcastedResults);

        $resultBroadcastedCategories = $this->service()->filterCategoriesByBroadcastedDate(
            [$category1, $category2],
            $start,
            $end
        );


        $this->assertCount(1, $resultBroadcastedCategories);
        $this->assertSame([1,2,3], $resultBroadcastedCategories[0]->getDbAncestryIds());
    }
}
