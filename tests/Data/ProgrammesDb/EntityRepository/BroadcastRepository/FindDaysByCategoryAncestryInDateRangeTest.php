<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use DateTime;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository::<public>
 */
class FindDaysByCategoryAncestryInDateRangeTestTest extends AbstractDatabaseTest
{
    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }
    /**
     * @dataProvider findDaysByCategoryAncestryInDateRangeDataProvider
     */
    public function testFindDaysByCategoryAncestryInDateRangeMultipleEntries($pipId, $type, $medium, $from, $to, $expectedOutput)
    {
        $this->loadFixtures(['BroadcastsWithCategoriesFixture']);
        $this->enableEmbargoedFilter();

        /** @var BroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        $ancestry = $this->getAncestryFromPersistentIdentifier($pipId, 'Category', 'pipId');

        $data = $repo->findDaysByCategoryAncestryInDateRange([$ancestry], $type, $medium, $from, $to);


        $this->assertSame($expectedOutput, $data);
        $this->assertCount(1, $this->getDbQueries());
    }

    public function findDaysByCategoryAncestryInDateRangeDataProvider()
    {
        return [
            [
                'c0000001', // ancestryId = 1
                'Broadcast',
                null,
                new DateTimeImmutable('2011-07-01 00:00:00'),
                new DateTimeImmutable('2011-10-01 00:00:00'),
                [
                    ['ancestry' => '1,', 'day' => '5', 'month' => '7', 'year' => '2011'],
                    ['ancestry' => '1,', 'day' => '5', 'month' => '8', 'year' => '2011'],
                    ['ancestry' => '1,2,', 'day' => '5', 'month' => '8', 'year' => '2011'],
                    ['ancestry' => '1,', 'day' => '5', 'month' => '9', 'year' => '2011'],
                ],
            ],
            [
                'c0000001', // ancestryId = 1
                'Broadcast',
                'radio',
                new DateTimeImmutable('2011-07-01 00:00:00'),
                new DateTimeImmutable('2011-10-01 00:00:00'),
                [
                    ['ancestry' => '1,', 'day' => '5', 'month' => '7', 'year' => '2011'],
                ],
            ],
            [
                'c0000002', // ancestryId = 1,2,
                'Broadcast',
                null,
                new DateTimeImmutable('2011-07-01 00:00:00'),
                new DateTimeImmutable('2011-10-01 00:00:00'),
                [
                    ['ancestry' => '1,2,', 'day' => '5', 'month' => '8', 'year' => '2011'],
                ],
            ],
            [
                'c0000001', // ancestryId = 1
                'Webcast',
                null,
                new DateTimeImmutable('2011-06-01 00:00:00'),
                new DateTimeImmutable('2011-09-01 00:00:00'),
                [],
            ],
            [
                'c0000002', // ancestryId = 1,2,
                'Broadcast',
                null,
                new DateTimeImmutable('2013-06-01 00:00:00'),
                new DateTimeImmutable('2013-08-01 00:00:00'),
                [],
            ], // embargoed
        ];
    }

    public function testFindDaysByCategoryAncestryInDateRangeWhenEmbargoIsDisabled()
    {
        $this->loadFixtures(['BroadcastsWithCategoriesFixture']);
        $this->disableEmbargoedFilter();
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        $ancestry = $this->getAncestryFromPersistentIdentifier('c0000001', 'Category', 'pipId');

        $data = $repo->findDaysByCategoryAncestryInDateRange(
            [$ancestry],
            'Any',
            null,
            new DateTimeImmutable('2013-07-01 00:00:00'),
            new DateTimeImmutable('2013-08-01 00:00:00')
        );

        $this->assertSame([
            ['ancestry' => '1,', 'day' => '5', 'month' => '7', 'year' => '2013'],
            ['ancestry' => '1,2,', 'day' => '5', 'month' => '7', 'year' => '2013'],
            ['ancestry' => '1,2,3,', 'day' => '5', 'month' => '7', 'year' => '2013'],
        ], $data);

        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindDaysByCategoryAncestryInDateRangeWhenEmptyResultSet()
    {
        $this->loadFixtures([]);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        $entities = $repo->findDaysByCategoryAncestryInDateRange(
            [[1]],
            'Any',
            null,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->assertEquals([], $entities);

        $this->assertCount(1, $this->getDbQueries());
    }
}
