<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class FindByProgrammeTest extends AbstractDatabaseTest
{
    public function tearDown(): void
    {
        $this->disableEmbargoedFilter();
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

    public function testFindByProgramme(): void
    {
        $this->loadFixtures(['CollapsedBroadcastsWithCategoriesFixture']);
        $this->enableEmbargoedFilter();

        /** @var CollapsedBroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        foreach ($this->findByProgrammeData() as $test) {
            [$pid, $isWebcastOnly, $limit, $offset, $expectedOutput] = $test;

            $ancestry = $this->getAncestryFromPersistentIdentifier($pid, 'CoreEntity');

            $data = $repo->findByProgramme(end($ancestry), $isWebcastOnly, $limit, $offset);

            $this->assertSame(count($expectedOutput), count($data));
            $this->assertEquals(array_column($expectedOutput, 'startAt'), array_column($data, 'startAt'));
            $this->assertEquals(array_column($expectedOutput, 'endAt'), array_column($data, 'endAt'));
            $this->assertSame(array_column($expectedOutput, 'serviceIds'), array_column($data, 'serviceIds'));

            $this->resetDbQueryLogger();
        }
    }

    public function findByProgrammeData(): array
    {
        return [
            // type
            [
                'p0000003',
                true,
                null,
                0,
                [
                    [
                        'startAt' => new DateTimeImmutable('2017-02-06 09:31'),
                        'endAt' => new DateTimeImmutable('2017-02-06 10:30'),
                        'serviceIds' => ['27', '28'],
                    ],
                ],
            ],
            // limit
            [
                'p0000001',
                false,
                1,
                0,
                [
                    [
                        'startAt' => new DateTimeImmutable('2017-01-04 09:30'),
                        'endAt' => new DateTimeImmutable('2017-01-04 10:30'),
                        'serviceIds' => ['7', '8'],
                    ],
                ],
            ],
            // offset
            [
                'p0000001',
                false,
                null,
                1,
                [
                    [
                        'startAt' => new DateTimeImmutable('2017-01-05 09:30'),
                        'endAt' => new DateTimeImmutable('2017-01-05 10:30'),
                        'serviceIds' => ['3', '4'],
                    ],
                ],
            ],
        ];
    }
}
