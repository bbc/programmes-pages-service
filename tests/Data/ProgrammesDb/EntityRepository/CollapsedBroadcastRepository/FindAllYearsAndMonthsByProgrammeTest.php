<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository::<public>
 */
class FindAllYearsAndMonthsByProgrammeTest extends AbstractDatabaseTest
{
    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function testFindAllYearsAndMonthsByProgramme()
    {
        $this->loadFixtures(['CollapsedBroadcastMonthsFixture']);
        $this->enableEmbargoedFilter();

        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        foreach ($this->findAllYearsAndMonthsByProgrammeData() as $data) {
            list($pid, $type, $expectedOutput) = $data;

            $ancestry = $this->getAncestryFromPersistentIdentifier($pid, 'CoreEntity');
            $data = $repo->findAllYearsAndMonthsByProgramme($ancestry, $type);
            $this->assertSame($expectedOutput, $data);

            // findAllYearsAndMonthsByProgramme query only
            $this->assertCount(1, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }

    public function findAllYearsAndMonthsByProgrammeData()
    {
        return [
            ['p0000001', false, [['year' => '2016', 'month' => '7'], ['year' => '2015', 'month' => '6']] ],
            ['p0000001', true, [['year' => '2014', 'month' => '6']] ],
            ['p0000003', false, []], // embargoed
        ];
    }

    public function testFindAllYearsAndMonthsByProgrammeDataWhenEmbargoIsDisabled()
    {
        $this->loadFixtures(['CollapsedBroadcastMonthsFixture']);
        $this->disableEmbargoedFilter();
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        $ancestry = $this->getAncestryFromPersistentIdentifier('p0000003', 'CoreEntity');

        $data = $repo->findAllYearsAndMonthsByProgramme($ancestry, false);
        $this->assertSame([['year' => '2011', 'month' => '7']], $data);

        // findAllYearsAndMonthsByProgramme query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindAllYearsAndMonthsByProgrammeWhenEmptyResultSet()
    {
        $this->loadFixtures([]);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        $entities = $repo->findAllYearsAndMonthsByProgramme([1], false);
        $this->assertEquals([], $entities);

        // findAllYearsAndMonthsByProgramme query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
