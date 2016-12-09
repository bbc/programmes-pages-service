<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use DateTimeImmutable;

class CountByCategoryAncestryAndEndingAfterTest extends AbstractDatabaseTest
{
    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function countByCategoryAncestryAndEndingAfter()
    {
        return [
            [
                [1], // music
                'Broadcast',
                null,
                new DateTimeImmutable('2016-07-01'),
                new DateTimeImmutable('2016-07-31'),
                1,
            ],
            [
                [1, 2], // jazzandblues, music
                'Broadcast',
                null,
                new DateTimeImmutable('2011-07-01'),
                new DateTimeImmutable('2011-07-31'),
                1,
            ],
        ];
    }

    public function testCountByCategoryAncestryAndEndingAfterTest()
    {
        $this->loadFixtures(['BroadcastsWithCategoriesFixture']);
        $this->enableEmbargoedFilter();

        /** @var BroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        foreach ($this->countByCategoryAncestryAndEndingAfter() as $data) {
            list($categoryAncestry, $type, $medium, $from, $to, $expectedOutput) = $data;

            $data = $repo->countByCategoryAncestryAndEndingAfter($categoryAncestry, $type, $medium, $from, $to);
            $this->assertSame($expectedOutput, $data);

            // countByCategoryAncestryAndEndingAfter query only
            $this->assertCount(1, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }
}
