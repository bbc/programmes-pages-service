<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use DateTime;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository::<public>
 */
class FindUsedDaysByCategoryAncestryInDateRangeTest extends AbstractDatabaseTest
{
    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function testFindAllYearsAndMonthsByProgramme()
    {
        $this->loadFixtures(['BroadcastsWithCategoriesFixture']);
        $this->enableEmbargoedFilter();

        /** @var BroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        foreach ($this->findAllYearsAndMonthsByProgrammeData() as $data) {
            list($pipId, $type, $medium, $from, $to, $expectedOutput) = $data;

            $ancestry = $this->getAncestryFromPersistentIdentifier($pipId, 'Category', 'PipId');

            $data = $repo->findUsedDaysByCategoryAncestryInDateRange($ancestry, $type, $medium, $from, $to);
            $this->assertSame($expectedOutput, $data);

            // findAllYearsAndMonthsByProgramme query only
            $this->assertCount(1, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }

    public function findAllYearsAndMonthsByProgrammeData()
    {
        return [
            [
                'c0000001',
                'Broadcast',
                null,
                new DateTimeImmutable('2011-07-01 00:00:00'),
                new DateTimeImmutable('2011-10-01 00:00:00'),
                [
                    ['day' => '5', 'month' => '7'],
                    ['day' => '5', 'month' => '9'],
                    ['day' => '5', 'month' => '8'],
                ],
            ],
            [
                'c0000001',
                'Broadcast',
                'radio',
                new DateTimeImmutable('2011-07-01 00:00:00'),
                new DateTimeImmutable('2011-10-01 00:00:00'),
                [
                    ['day' => '5', 'month' => '7'],
                ],
            ],
            [
                'c0000002',
                'Broadcast',
                null,
                new DateTimeImmutable('2011-07-01 00:00:00'),
                new DateTimeImmutable('2011-10-01 00:00:00'),
                [
                    ['day' => '5', 'month' => '8'],
                ],
            ],
            [
                'c0000001',
                'Webcast',
                null,
                new DateTimeImmutable('2011-06-01 00:00:00'),
                new DateTimeImmutable('2011-09-01 00:00:00'),
                [],
            ],
            [
                'c0000002',
                'Broadcast',
                null,
                new DateTimeImmutable('2013-06-01 00:00:00'),
                new DateTimeImmutable('2013-08-01 00:00:00'),
                [],
            ], // embargoed
        ];
    }

    public function testFindAllYearsAndMonthsByProgrammeDataWhenEmbargoIsDisabled()
    {
        $this->loadFixtures(['BroadcastsWithCategoriesFixture']);
        $this->disableEmbargoedFilter();
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        $ancestry = $this->getAncestryFromPersistentIdentifier('c0000001', 'Category', 'PipId');

        $data = $repo->findUsedDaysByCategoryAncestryInDateRange(
            $ancestry,
            'Any',
            null,
            new DateTimeImmutable('2013-07-01 00:00:00'),
            new DateTimeImmutable('2013-08-01 00:00:00')
        );

        $this->assertSame([['day' => '5', 'month' => '7']], $data);

        // findAllYearsAndMonthsByProgramme query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindAllYearsAndMonthsByProgrammeWhenEmptyResultSet()
    {
        $this->loadFixtures([]);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        $entities = $repo->findUsedDaysByCategoryAncestryInDateRange(
            [1],
            'Any',
            null,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $this->assertEquals([], $entities);

        // findAllYearsAndMonthsByProgramme query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
