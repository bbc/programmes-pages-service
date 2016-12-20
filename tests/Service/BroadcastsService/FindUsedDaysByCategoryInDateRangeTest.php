<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use DateTimeImmutable;
use PHPUnit_Framework_TestCase;

class FindUsedDaysByCategoryInDateRangeTest extends AbstractBroadcastsServiceTest
{
    public function testFindUsedDaysByCategoryInDateRange()
    {
        $dbAncestry = [1, 2, 3];
        $category = $this->mockEntity('Genre', 3);
        $category->method('getDbAncestryIds')->willReturn($dbAncestry);

        $dbData = [
            ['day' => '1', 'month' => '8'],
            ['day' => '2', 'month' => '8'],
            ['day' => '3', 'month' => '8'],
            ['day' => '1', 'month' => '9'],
            ['day' => '2', 'month' => '9'],
            ['day' => '4', 'month' => '9'],
            ['day' => '5', 'month' => '10'],
        ];

        $expectedResult = [
            8 => [1, 2, 3],
            9 => [1, 2, 4],
            10 => [5],
        ];

        $start = new DateTimeImmutable();
        $end = new DateTimeImmutable();

        $this->mockRepository->expects($this->once())
            ->method('findUsedDaysByCategoryAncestryInDateRange')
            ->with($dbAncestry, 'Broadcast', null, $start, $end)
            ->willReturn($dbData);

        $result = $this->service()->findUsedDaysByCategoryInDateRange($category, $start, $end);
        $this->assertSame($expectedResult, $result);
    }
}
