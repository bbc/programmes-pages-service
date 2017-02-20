<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class FindByCategoryAncestryAndStartAtDateRangeTest extends AbstractDatabaseTest
{
    public function tearDown(): void
    {
        $this->disableEmbargoedFilter();
    }

    public function testFindByCategoryAncestryAndStartAtDateRange(): void
    {
        $this->loadFixtures(['CollapsedBroadcastsWithCategoriesFixture']);
        $this->enableEmbargoedFilter();

        /** @var CollapsedBroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        foreach ($this->findByCategoryAncestryAndStartAtDateRangeData() as $test) {
            [$categoryId, $isWebcastOnly, $from, $to, $limit, $offset, $expectedOutput, $numQueries] = $test;

            $categoryAncestry = $this->getAncestryFromPersistentIdentifier($categoryId, 'Category', 'pipId');

            $data = $repo->findByCategoryAncestryAndStartAtDateRange($categoryAncestry, $isWebcastOnly, $from, $to, $limit, $offset);

            $this->assertSame(count($expectedOutput), count($data));
            $this->assertEquals(array_column($expectedOutput, 'startAt'), array_column($data, 'startAt'));
            $this->assertEquals(array_column($expectedOutput, 'endAt'), array_column($data, 'endAt'));
            $this->assertSame(array_column($expectedOutput, 'programmePid'), array_column(array_column($data, 'programmeItem'), 'pid'));
            $this->assertSame(array_column($expectedOutput, 'serviceIds'), array_column($data, 'serviceIds'));

            // findByCategoryAncestryAndStartAtDateRange and ancestry hydration queries
            $this->assertCount($numQueries, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }

    public function findByCategoryAncestryAndStartAtDateRangeData(): array
    {
        return [
            // category
            [
                'c0000001', // music
                false,
                new DateTimeImmutable('2017-01-05'),
                new DateTimeImmutable('2017-02-07'),
                null,
                0,
                [
                    [
                        'startAt' => new DateTimeImmutable('2017-01-05 09:30'),
                        'endAt' => new DateTimeImmutable('2017-01-05 10:30'),
                        'programmePid' => 'p0000001',
                        'serviceIds' => ['3', '4'],
                    ],
                    [
                        'startAt' => new DateTimeImmutable('2017-01-06 09:30'),
                        'endAt' => new DateTimeImmutable('2017-01-06 10:30'),
                        'programmePid' => 'p0000006',
                        'serviceIds' => ['11', '12'],
                    ],
                    [
                        'startAt' => new DateTimeImmutable('2017-02-06 09:30'),
                        'endAt' => new DateTimeImmutable('2017-02-06 10:30'),
                        'programmePid' => 'p0000003',
                        'serviceIds' => ['15', '16'],
                    ],
                ],
                2,
            ],
            // time
            [
                'c0000001', // music
                false,
                new DateTimeImmutable('2017-02-05'),
                new DateTimeImmutable('2017-02-07'),
                null,
                0,
                [
                    [
                        'startAt' => new DateTimeImmutable('2017-02-06 09:30'),
                        'endAt' => new DateTimeImmutable('2017-02-06 10:30'),
                        'programmePid' => 'p0000003',
                        'serviceIds' => ['15', '16'],
                    ],
                ],
                2,
            ],
            // isWebcastOnly
            [
                'c0000001', // music
                true,
                new DateTimeImmutable('2017-01-05'),
                new DateTimeImmutable('2017-02-07'),
                null,
                0,
                [
                    [
                        'startAt' => new DateTimeImmutable('2017-02-06 09:30'),
                        'endAt' => new DateTimeImmutable('2017-02-06 10:30'),
                        'programmePid' => 'p0000003',
                        'serviceIds' => ['27', '28'],
                    ],
                ],
                2,
            ],
            // subcategory
            [
                'c0000002', // jazz and blues
                false,
                new DateTimeImmutable('2017-01-05'),
                new DateTimeImmutable('2017-02-07'),
                null,
                0,
                [
                    [
                        'startAt' => new DateTimeImmutable('2017-02-06 09:30'),
                        'endAt' => new DateTimeImmutable('2017-02-06 10:30'),
                        'programmePid' => 'p0000003',
                        'serviceIds' => ['15', '16'],
                    ],
                ],
                2,
            ],
            // invalid category
            [
                'c5810338', // invalid
                false,
                new DateTimeImmutable('2017-01-05'),
                new DateTimeImmutable('2017-02-07'),
                null,
                0,
                [],
                1,
            ],
            // EndAt out of range
            [
                'c0000001', // music
                false,
                new DateTimeImmutable('2018-01-05'),
                new DateTimeImmutable('2018-02-07'),
                null,
                0,
                [],
                1,
            ],
            // Offset
            [
                'c0000001', // music
                false,
                new DateTimeImmutable('2017-01-05'),
                new DateTimeImmutable('2017-02-07'),
                null,
                2,
                [
                    [
                        'startAt' => new DateTimeImmutable('2017-02-06 09:30'),
                        'endAt' => new DateTimeImmutable('2017-02-06 10:30'),
                        'programmePid' => 'p0000003',
                        'serviceIds' => ['15', '16'],
                    ],
                ],
                2,
            ],
            // Limit
            [
                'c0000001', // music
                false,
                new DateTimeImmutable('2017-01-05'),
                new DateTimeImmutable('2017-02-07'),
                1,
                0,
                [
                    [
                        'startAt' => new DateTimeImmutable('2017-01-05 09:30'),
                        'endAt' => new DateTimeImmutable('2017-01-05 10:30'),
                        'programmePid' => 'p0000001',
                        'serviceIds' => ['3', '4'],
                    ],
                ],
                1,
            ],
        ];
    }
}
