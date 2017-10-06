<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository::<public>
 */
class CountUpcomingByProgrammeTest extends AbstractDatabaseTest
{
    public function tearDown(): void
    {
        $this->disableEmbargoedFilter();
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

    public function testCountUpcomingByProgramme(): void
    {
        $this->loadFixtures(['CollapsedBroadcastsWithCategoriesFixture']);
        $this->enableEmbargoedFilter();

        /** @var CollapsedBroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        foreach ($this->countUpcomingByProgrammeData() as $test) {
            [$pid, $isWebcastOnly, $date, $expectedOutput] = $test;

            $ancestry = $this->getAncestryFromPersistentIdentifier($pid, 'CoreEntity');

            $data = $repo->countUpcomingByProgramme($ancestry, $isWebcastOnly, $date);
            $this->assertSame($expectedOutput, $data);

            // countUpcomingByProgramme query only
            $this->assertCount(1, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }

    public function countUpcomingByProgrammeData(): array
    {
        return [
            ['p0000001', false, new DateTimeImmutable('2017-01-04T00:00:00'), 2],
            ['p0000001', false, new DateTimeImmutable('2017-01-05T00:00:00'), 1],
            ['p0000003', true, new DateTimeImmutable('2017-01-06T00:00:00'), 1],
            ['p0000002', false, new DateTimeImmutable('2017-02-06T00:00:00'), 0], // embargoed
        ];
    }

    public function testCountUpcomingByProgrammeWhenEmptyResultSet(): void
    {
        $this->loadFixtures([]);

        /** @var CollapsedBroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        $entities = $repo->countUpcomingByProgramme([1], true, new DateTimeImmutable());
        $this->assertEquals(0, $entities);

        // countUpcomingByProgramme query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
