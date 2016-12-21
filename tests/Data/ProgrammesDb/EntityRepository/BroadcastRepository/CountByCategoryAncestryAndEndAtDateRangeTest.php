<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use DateTimeImmutable;

class CountByCategoryAncestryAndEndAtDateRangeTest extends AbstractDatabaseTest
{
    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function countByCategoryAncestryAndEndAtDateRange()
    {
        return [
            [
                'c0000001', // music
                'Broadcast',
                null,
                new DateTimeImmutable('2016-07-01'),
                new DateTimeImmutable('2016-07-31'),
                1,
            ],
            [
                'c0000002', // jazzandblues, music
                'Broadcast',
                null,
                new DateTimeImmutable('2011-08-01'),
                new DateTimeImmutable('2011-08-31'),
                1,
            ],
        ];
    }

    public function testCountByCategoryAncestryAndEndAtDateRange()
    {
        $this->loadFixtures(['BroadcastsWithCategoriesFixture']);
        $this->enableEmbargoedFilter();

        /** @var BroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        foreach ($this->countByCategoryAncestryAndEndAtDateRange() as $data) {
            list($categoryId, $type, $medium, $from, $to, $expectedOutput) = $data;

            $categoryAncestry = $this->getAncestryFromPersistentIdentifier($categoryId, 'Category', 'pipId');

            $data = $repo->countByCategoryAncestryAndEndAtDateRange($categoryAncestry, $type, $medium, $from, $to);
            $this->assertSame($expectedOutput, $data);

            // countByCategoryAncestryAndEndingAfter query only
            $this->assertCount(1, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }
}
