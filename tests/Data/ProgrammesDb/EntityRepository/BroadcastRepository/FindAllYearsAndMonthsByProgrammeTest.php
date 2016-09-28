<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository::<public>
 */
class FindAllYearsAndMonthByProgrammeTest extends AbstractDatabaseTest
{
    public function testFindAllYearsAndMonthsByProgramme()
    {
        $this->loadFixtures(['BroadcastsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        foreach ($this->findAllYearsAndMonthsByProgrammeData() as $data) {
            list($pid, $expectedOutput) = $data;

            $ancestry = $this->getCoreEntityAncestry($pid);

            $data = $repo->findAllYearsAndMonthsByProgramme($ancestry);
            $this->assertSame($expectedOutput, $data);

            // findAllYearsAndMonthsByProgramme query only
            $this->assertCount(1, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }

    public function findAllYearsAndMonthsByProgrammeData()
    {
        return [
            ['p0000001', [['year' => '2016', 'month' => '7'], ['year' => '2011', 'month' => '7']] ],
            ['p0000002', []], // embargoed
        ];
    }

    public function testFindAllYearsAndMonthsByProgrammeWhenEmptyResultSet()
    {
        $this->loadFixtures([]);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        $entities = $repo->findAllYearsAndMonthsByProgramme([1]);
        $this->assertEquals([], $entities);

        // findAllYearsAndMonthsByProgramme query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
