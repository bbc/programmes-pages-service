<?php
namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use DateTimeImmutable;

class FilterCategoriesByBroadcastedDateTest extends AbstractCollapsedBroadcastServiceTest
{
    public function testRepositoryReceiveNeededParamsToFetchBroadcastsByDate()
    {
        $stubCat1 = $this->createConfiguredMock(Genre::class, ['getDbAncestryIds' => [1, 2, 3]]);
        $stubCat2 = $this->createConfiguredMock(Genre::class, ['getDbAncestryIds' => [5, 6, 7]]);

        $startDateTime = new DateTimeImmutable();
        $endDateTime = new DateTimeImmutable();

        $this->mockRepository
            ->expects($this->once())
            ->method('filterCategoriesByBroadcastedDates')
            ->with(
                [$stubCat1->getDbAncestryIds(), $stubCat2->getDbAncestryIds()],
                false,
                $startDateTime,
                $endDateTime
            );

        $this->service()->filterCategoriesByBroadcastedDate([$stubCat1, $stubCat2], $startDateTime, $endDateTime);
    }

    /**
     * @dataProvider categoriesBroadcastedOnDateProvider
     */
    public function testCategoriesBroadcastedOnDateAreFiltered($expectedCategoriesFoundOnDate, $dbResults)
    {
        $this->mockRepository->method('filterCategoriesByBroadcastedDates')->willReturn($dbResults);

        $categories = $this->service()->filterCategoriesByBroadcastedDate(
            [
                $this->createConfiguredMock(Genre::class, ['getDbAncestryIds' => [1, 2, 3]]),
                $this->createConfiguredMock(Genre::class, ['getDbAncestryIds' => [19, 20, 30, 40]]),
            ],
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->assertContainsOnly(Genre::class, $categories);
        $this->assertEquals($expectedCategoriesFoundOnDate, $this->extractDbAncestryIds($categories));
    }

    public function categoriesBroadcastedOnDateProvider(): array
    {
        return [
            'CASE: none of category specified was not broadcasted on date' => [
                [],
                [['ancestry' => '1,'], ['ancestry' => '1,2,']],
            ],
            'CASE: none of category specified was not broadcasted on date' => [
                [],
                [['ancestry' => '1,'], ['ancestry' => '1,2'], ['ancestry' => '1,2,3,4,']],
            ],
            'CASE: one of the specified category was broadcasted on date' => [
                [[1, 2, 3]],
                [['ancestry' => '1,'], ['ancestry' => '1,2'], ['ancestry' => '1,2,3,'], ['ancestry' => '1,2,3,4,']],
            ],
            'CASE: two of the specified category was broadcasted on date' => [
                [[1, 2, 3], [19, 20, 30, 40]],
                [['ancestry' => '1,2,'], ['ancestry' => '1,2,3,'], ['ancestry' => '1,2,3,4,'], ['ancestry' => '19,20,30,40,'], ['ancestry' => '5,6,7,']],
            ],
        ];
    }
}
