<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use DateTimeImmutable;

class FindDaysByCategoryInDateRangeTest extends AbstractCollapsedBroadcastServiceTest
{
    public function testFindUsedDaysByCategoryInDateRange()
    {
        $dbAncestry = [1, 2, 3];
        $category = $this->mockEntity('Genre', 3);
        $category->method('getDbAncestryIds')->willReturn($dbAncestry);

        $dbData = [
            ['day' => '1', 'month' => '8', 'year' => '2011'],
            ['day' => '2', 'month' => '8', 'year' => '2011'],
            ['day' => '2', 'month' => '8', 'year' => '2011'],
        ];

        $expectedResult = [
            '2011' => [
                '8' => [1, 2],
            ],
        ];

        $start = new DateTimeImmutable();
        $end = new DateTimeImmutable();

        $this->mockRepository->expects($this->once())
            ->method('findBroadcastedDatesForCategory')
            ->with($dbAncestry, false, $start, $end)
            ->willReturn($dbData);

        $result = $this->service()->findDaysByCategoryInDateRange($category, $start, $end);
        $this->assertSame($expectedResult, $result);
    }
}
