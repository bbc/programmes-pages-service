<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class FindUpcomingByProgrammeTest extends AbstractDatabaseTest
{
    public function tearDown(): void
    {
        $this->disableEmbargoedFilter();
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

    public function testFindUpcomingByProgramme()
    {
        $this->loadFixtures(['CollapsedBroadcastsWithCategoriesFixture']);
        $this->enableEmbargoedFilter();

        /** @var CollapsedBroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        foreach ($this->findUpcomingByProgrammeData() as $test) {
            [$pid, $isWebcastOnly, $cutoffTime, $limit, $offset, $expectedOutput, $numQueries] = $test;

            $ancestry = $this->getAncestryFromPersistentIdentifier($pid, 'CoreEntity');

            $data = $repo->findUpcomingByProgramme($ancestry, $isWebcastOnly, $cutoffTime, $limit, $offset);

            $this->assertSame(count($expectedOutput), count($data));
            $this->assertEquals(array_column($expectedOutput, 'startAt'), array_column($data, 'startAt'));
            $this->assertEquals(array_column($expectedOutput, 'endAt'), array_column($data, 'endAt'));
            $this->assertSame(array_column($expectedOutput, 'programmePid'), array_column(array_column($data, 'programmeItem'), 'pid'));
            $this->assertSame(array_column($expectedOutput, 'serviceIds'), array_column($data, 'serviceIds'));

            // findByProgrammeAndMonth and ancestry hydration queries
            $this->assertCount($numQueries, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }

    public function findUpcomingByProgrammeData(): array
    {
        return [
            // working date
            [
                'p0000001',
                false,
                new DateTimeImmutable('2017-01-04'),
                null,
                0,
                [
                    [
                        'startAt' => new DateTimeImmutable('2017-01-04 09:30'),
                        'endAt' => new DateTimeImmutable('2017-01-04 10:30'),
                        'programmePid' => 'p0000001',
                        'serviceIds' => ['7', '8'],
                    ],
                    [
                        'startAt' => new DateTimeImmutable('2017-01-05 09:30'),
                        'endAt' => new DateTimeImmutable('2017-01-05 10:30'),
                        'programmePid' => 'p0000001',
                        'serviceIds' => ['3', '4'],
                    ],
                ],
                1,
            ],
            // type
            [
                'p0000003',
                true,
                new DateTimeImmutable('2017-02-04'),
                null,
                0,
                [
                    [
                        'startAt' => new DateTimeImmutable('2017-02-06 09:31'),
                        'endAt' => new DateTimeImmutable('2017-02-06 10:30'),
                        'programmePid' => 'p0000003',
                        'serviceIds' => ['27', '28'],
                    ],
                ],
                2,
            ],
            // limit
            [
                'p0000001',
                false,
                new DateTimeImmutable('2017-01-04'),
                1,
                0,
                [
                    [
                        'startAt' => new DateTimeImmutable('2017-01-04 09:30'),
                        'endAt' => new DateTimeImmutable('2017-01-04 10:30'),
                        'programmePid' => 'p0000001',
                        'serviceIds' => ['7', '8'],
                    ],
                ],
                1,
            ],
            // offset
            [
                'p0000001',
                false,
                new DateTimeImmutable('2017-01-04'),
                null,
                1,
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
            // non-working date
            [
                'p0000001',
                false,
                new DateTimeImmutable('2018-01-04'),
                null,
                0,
                [],
                1,
            ],
        ];
    }
}
