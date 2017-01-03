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
            ['ancestry' => '1,2,3', 'day' => '1', 'month' => '8', 'year' => '2011'],
            ['ancestry' => '1,2,3', 'day' => '2', 'month' => '8', 'year' => '2011'],
            ['ancestry' => '1,2,3', 'day' => '3', 'month' => '8', 'year' => '2011'],
            ['ancestry' => '1,2,3', 'day' => '1', 'month' => '9', 'year' => '2011'],
            ['ancestry' => '1,2,3', 'day' => '2', 'month' => '9', 'year' => '2011'],
            ['ancestry' => '1,2,3', 'day' => '4', 'month' => '9', 'year' => '2011'],
            ['ancestry' => '1,2,3', 'day' => '5', 'month' => '10', 'year' => '2011'],
        ];

        $expectedResult = [
            2011 => [
                8 => [1, 2, 3],
                9 => [1, 2, 4],
                10 => [5],
            ],
        ];

        $start = new DateTimeImmutable();
        $end = new DateTimeImmutable();

        $this->mockRepository->expects($this->once())
            ->method('findDaysByCategoryAncestryInDateRange')
            ->with([$dbAncestry], 'Broadcast', null, $start, $end)
            ->willReturn($dbData);

        $result = $this->service()->findDaysByCategoryInDateRange([$category], $start, $end);

        $this->assertSame($expectedResult, $result);
    }
}
