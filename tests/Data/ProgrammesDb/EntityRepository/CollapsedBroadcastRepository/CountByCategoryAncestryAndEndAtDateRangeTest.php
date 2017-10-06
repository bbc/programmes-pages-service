<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class CountByCategoryAncestryAndEndAtDateRangeTest extends AbstractDatabaseTest
{
    public function tearDown(): void
    {
        $this->disableEmbargoedFilter();
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

    public function testCountByCategoryAncestryAndEndAtDateRange(): void
    {
        $this->loadFixtures(['CollapsedBroadcastsWithCategoriesFixture']);
        $this->enableEmbargoedFilter();

        /** @var CollapsedBroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        foreach ($this->countByCategoryAncestryAndEndAtDateRangeData() as $test) {
            [$categoryId, $isWebcastOnly, $from, $to, $expectedOutput] = $test;

            $categoryAncestry = $this->getAncestryFromPersistentIdentifier($categoryId, 'Category', 'pipId');

            $data = $repo->countByCategoryAncestryAndEndAtDateRange($categoryAncestry, $isWebcastOnly, $from, $to);
            $this->assertSame($expectedOutput, $data);

            // countByCategoryAncestryAndEndAtDateRange query only
            $this->assertCount(1, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }

    public function countByCategoryAncestryAndEndAtDateRangeData(): array
    {
        return [
            [
                'c0000001',
                false,
                new DateTimeImmutable('2017-01-05'),
                new DateTimeImmutable('2017-02-07'),
                3,
            ],
            [
                'c0000002',
                false,
                new DateTimeImmutable('2017-01-05'),
                new DateTimeImmutable('2017-02-07'),
                1,
            ],
            [
                'c0000001',
                false,
                new DateTimeImmutable('2018-01-05'),
                new DateTimeImmutable('2018-02-07'),
                0,
            ],
            [
                'c0000001',
                true,
                new DateTimeImmutable('2017-01-05'),
                new DateTimeImmutable('2017-02-07'),
                1,
            ],
        ];
    }

    public function testCountByCategoryAncestryAndEndAtDateRangeWhenEmptyResultSet(): void
    {
        $this->loadFixtures([]);

        /** @var CollapsedBroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        $entities = $repo->countByCategoryAncestryAndEndAtDateRange([1], false, new DateTimeImmutable(), new DateTimeImmutable());
        $this->assertEquals(0, $entities);

        // countUpcomingByProgramme query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
