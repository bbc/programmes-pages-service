<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class FindBroadcastedDatesForCategoryTest extends AbstractDatabaseTest
{
    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }
    /**
     * @dataProvider findDaysByCategoryAncestryInDateRangeDataProvider
     */
    public function testFindDaysByCategoryAncestryInDateRangeMultipleCases($pipId, $isWebcastOnly, $from, $to, $expectedOutput)
    {
        $this->loadFixtures(['CollapsedBroadcastsWithCategoriesFixture']);
        $this->enableEmbargoedFilter();

        /** @var BroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        $ancestry = $this->getAncestryFromPersistentIdentifier($pipId, 'Category', 'pipId');

        $data = $repo->findBroadcastedDatesForCategory($ancestry, $isWebcastOnly, $from, $to);
        //var_dump($expectedOutput);
        //var_dump($data);

        $this->assertSame($expectedOutput, $data);
    }

    public function findDaysByCategoryAncestryInDateRangeDataProvider()
    {
        return [
            [
                'c0000001', // ancestryid = 1
                false,
                new DateTimeImmutable('2017-01-01 00:00:00'),
                new DateTimeImmutable('2017-03-01 00:00:00'),
                [
                    ['day' => '4', 'month' => '1', 'year' => '2017'],
                    ['day' => '5', 'month' => '1', 'year' => '2017'],
                    ['day' => '6', 'month' => '1', 'year' => '2017'],
                    ['day' => '6', 'month' => '2', 'year' => '2017'],
                ],
            ],
            [
                'c0000002',  // ancestryid = 1,2
                false,
                new DateTimeImmutable('2017-01-01 00:00:00'),
                new DateTimeImmutable('2017-03-01 00:00:00'),
                [
                    ['day' => '6', 'month' => '2', 'year' => '2017'],
                ],
            ],
            [
                'c0000001',  // ancestryid = 1
                true,
                new DateTimeImmutable('2017-01-01 00:00:00'),
                new DateTimeImmutable('2017-03-01 00:00:00'),
                [
                    ['day' => '6', 'month' => '2', 'year' => '2017'],
                ],
            ],
            [
                'c0000003',  // ancestryid = 1,2
                false,
                new DateTimeImmutable('2017-01-01 00:00:00'),
                new DateTimeImmutable('2017-03-01 00:00:00'),
                [],
            ], // embargoed
        ];
    }

    public function testFindDaysByCategoryAncestryInDateRangeWhenEmbargoIsDisabled()
    {
        $this->loadFixtures(['CollapsedBroadcastsWithCategoriesFixture']);
        $this->disableEmbargoedFilter();

        /** @var BroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        $ancestry = $this->getAncestryFromPersistentIdentifier('c0000001', 'Category', 'pipId');

        $data = $repo->findBroadcastedDatesForCategory(
            $ancestry,
            false,
            new DateTimeImmutable('2017-01-01 00:00:00'),
            new DateTimeImmutable('2017-03-01 00:00:00')
        );

        $this->assertSame([
            ['day' => '4', 'month' => '1', 'year' => '2017'],
            ['day' => '5', 'month' => '1', 'year' => '2017'],
            ['day' => '6', 'month' => '1', 'year' => '2017'],
            ['day' => '6', 'month' => '2', 'year' => '2017'],
            ['day' => '7', 'month' => '2', 'year' => '2017'],
        ], $data);

        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindDaysByCategoryAncestryInDateRangeWhenEmptyResultSet()
    {
        $this->loadFixtures([]);

        /** @var BroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        $entities = $repo->findBroadcastedDatesForCategory(
            [1],
            false,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->assertEquals([], $entities);
        $this->assertCount(1, $this->getDbQueries());
    }
}
