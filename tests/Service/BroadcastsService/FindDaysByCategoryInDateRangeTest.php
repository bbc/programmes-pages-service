<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use DateTimeImmutable;

class FindDaysByCategoryInDateRangeTest extends AbstractBroadcastsServiceTest
{
    public function testFindUsedDaysByCategoryInDateRange()
    {
        $dbAncestry = [1, 2, 3];
        $category = $this->mockEntity('Genre', 3);
        $category->method('getDbAncestryIds')->willReturn($dbAncestry);

        $dbData = [
            ['ancestry' => '1,2,3,', 'day' => '1', 'month' => '8', 'year' => '2011'],
            ['ancestry' => '1,2,3,4,', 'day' => '2', 'month' => '8', 'year' => '2011'],
            ['ancestry' => '1,2,3,5,', 'day' => '2', 'month' => '8', 'year' => '2011'],
        ];

        $expectedResult = [
            '2011' => [
                '8' => [1, 2],
            ],
        ];

        $start = new DateTimeImmutable();
        $end = new DateTimeImmutable();

        $this->mockRepository->expects($this->once())
             ->method('findBroadcastedDatesForCategories')
             ->with([$dbAncestry], 'Broadcast', null, $start, $end)
             ->willReturn($dbData);

        $resultBroadcastedCategories = $this->service()->findDaysByCategoryInDateRange(
            $category,
            $start,
            $end
        );

        $this->assertSame($expectedResult, $resultBroadcastedCategories);
    }
}
