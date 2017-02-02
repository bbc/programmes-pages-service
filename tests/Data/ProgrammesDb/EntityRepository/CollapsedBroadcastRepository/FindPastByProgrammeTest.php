<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class FindPastByProgrammeTest extends AbstractDatabaseTest
{
    public function tearDown(): void
    {
        $this->disableEmbargoedFilter();
    }

    public function testFindPastByProgramme(): void
    {
        $this->loadFixtures(['CollapsedBroadcastsWithCategoriesFixture']);
        $this->enableEmbargoedFilter();

        /** @var CollapsedBroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        foreach ($this->findPastByProgrammeData() as $test) {
            [$pid, $isWebcastOnly, $cutoffTime, $limit, $offset, $expectedOutput] = $test;

            $ancestry = $this->getAncestryFromPersistentIdentifier($pid, 'CoreEntity');

            $data = $repo->findPastByProgramme($ancestry, $isWebcastOnly, $cutoffTime, $limit, $offset);

            $this->assertSame(count($expectedOutput), count($data));
            $this->assertEquals(array_column($expectedOutput, 'startAt'), array_column($data, 'startAt'));
            $this->assertEquals(array_column($expectedOutput, 'endAt'), array_column($data, 'endAt'));
            $this->assertSame(array_column($expectedOutput, 'programmePid'), array_column(array_column($data, 'programmeItem'), 'pid'));
            $this->assertSame(array_column($expectedOutput, 'serviceIds'), array_column($data, 'serviceIds'));

            // findByProgrammeAndMonth query only
            $this->assertCount(1, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }

    public function findPastByProgrammeData(): array
    {
        return [
            // working date
            [
                'p0000001',
                false,
                new DateTimeImmutable('2017-01-06'),
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
                        'startAt' => new DateTimeImmutable('2017-01-04 09:30'),
                        'endAt' => new DateTimeImmutable('2017-01-04 10:30'),
                        'programmePid' => 'p0000001',
                        'serviceIds' => ['7', '8'],
                    ],
                ],
            ],
            // type
            [
                'p0000003',
                true,
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
            ],
            // limit
            [
                'p0000001',
                false,
                new DateTimeImmutable('2017-01-06'),
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
            ],
            // offset
            [
                'p0000001',
                false,
                new DateTimeImmutable('2017-01-06'),
                null,
                1,
                [
                    [
                        'startAt' => new DateTimeImmutable('2017-01-04 09:30'),
                        'endAt' => new DateTimeImmutable('2017-01-04 10:30'),
                        'programmePid' => 'p0000001',
                        'serviceIds' => ['7', '8'],
                    ],
                ],
            ],
            // non-working date
            [
                'p0000001',
                false,
                new DateTimeImmutable('2016-01-06'),
                null,
                0,
                [],
            ],
        ];
    }
}
