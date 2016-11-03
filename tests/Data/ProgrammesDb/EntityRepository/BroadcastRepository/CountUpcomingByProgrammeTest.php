<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use DateTimeImmutable;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository::<public>
 */
class CountUpcomingByProgrammeTest extends AbstractDatabaseTest
{
    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function testCountUpcomingByProgramme()
    {
        $this->loadFixtures(['BroadcastMonthsFixture']);
        $this->enableEmbargoedFilter();

        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        foreach ($this->countUpcomingByProgrammeData() as $data) {
            list($pid, $type, $date, $expectedOutput) = $data;

            $ancestry = $this->getCoreEntityAncestry($pid);

            $data = $repo->countUpcomingByProgramme($ancestry, $type, $date);
            $this->assertSame($expectedOutput, $data);

            // countUpcomingByProgramme query only
            $this->assertCount(1, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }

    public function countUpcomingByProgrammeData()
    {
        $date = new DateTimeImmutable('2014-01-01T00:00:00');
        return [
            ['p0000001', 'Any', $date, 5],
            ['p0000001', 'Broadcast', $date, 4],
            ['p0000001', 'Webcast', $date, 1],
            ['p0000002', 'Any', $date, 0], // embargoed
        ];
    }

    public function testCountUpcomingByProgrammeWhenEmptyResultSet()
    {
        $this->loadFixtures([]);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        $entities = $repo->countUpcomingByProgramme([1], 'Any', new DateTimeImmutable());
        $this->assertEquals(0, $entities);

        // countUpcomingByProgramme query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
