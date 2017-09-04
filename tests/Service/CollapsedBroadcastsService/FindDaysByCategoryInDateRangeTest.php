<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use DateTimeImmutable;

class FindDaysByCategoryInDateRangeTest extends AbstractCollapsedBroadcastServiceTest
{
    public function testFindUsedDaysByCategoryInDateRange()
    {
        $stubCategory = $this->createConfiguredMock(Genre::class, ['getDbAncestryIds' => [1, 2, 3]]);

        $start = new DateTimeImmutable();
        $end = new DateTimeImmutable();

        $this->mockRepository->expects($this->once())
            ->method('findBroadcastedDatesForCategory')
            ->with($stubCategory->getDbAncestryIds(), false, $start, $end);

        $this->service()->findDaysByCategoryInDateRange($stubCategory, $start, $end);
    }

    public function testResultsHasProperStructureAndIsOrdered()
    {
        $stubCategory = $this->createConfiguredMock(Genre::class, ['getDbAncestryIds' => [1, 2, 3]]);

        $this->mockRepository
            ->method('findBroadcastedDatesForCategory')
            ->willReturn([
                ['day' => '7', 'month' => '2', 'year' => '2010'],
                ['day' => '1', 'month' => '8', 'year' => '2011'],
                ['day' => '2', 'month' => '8', 'year' => '2011'],
                ['day' => '2', 'month' => '8', 'year' => '2011'],
        ]);

        $datesThatCategoryIsBroadcasted = $this->service()->findDaysByCategoryInDateRange($stubCategory, new DateTimeImmutable(), new DateTimeImmutable());

        $expectedResult = [
            '2011' => [
                '8' => [1, 2],
            ],
            '2010' => [
                '2' => [7]
            ]
        ];

        $this->assertEquals($expectedResult, $datesThatCategoryIsBroadcasted);
    }

    public function testResultIsEmptyWhenTheSpecifiedCategoryHasNotBeenBroadcastedOnThatPerio()
    {
        $this->mockRepository
            ->method('findBroadcastedDatesForCategory')
            ->willReturn([]);

        $datesThatCategoryIsBroadcasted = $this->service()->findDaysByCategoryInDateRange(
            $this->createMock(Genre::class),
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->assertEquals([], $datesThatCategoryIsBroadcasted);
    }
}
