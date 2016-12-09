<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

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
        $this->loadFixtures(['BroadcastMonthsFixture']);
        $this->enableEmbargoedFilter();

        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

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
            ['p0000001', 'Any', [['year' => '2016', 'month' => '7'], ['year' => '2015', 'month' => '6'], ['year' => '2015', 'month' => '5'], ['year' => '2014', 'month' => '6']] ],
            ['p0000001', 'Broadcast', [['year' => '2016', 'month' => '7'], ['year' => '2015', 'month' => '6'], ['year' => '2015', 'month' => '5']] ],
            ['p0000001', 'Webcast', [['year' => '2014', 'month' => '6']] ],
            ['p0000002', 'Any', []], // embargoed
        ];
    }

    public function testFindAllYearsAndMonthsByProgrammeDataWhenEmbargoIsDisabled()
    {
        $this->loadFixtures(['BroadcastMonthsFixture']);
        $this->disableEmbargoedFilter();
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        $ancestry = $this->getAncestryFromPersistentIdentifier('p0000002', 'CoreEntity');

        $data = $repo->findAllYearsAndMonthsByProgramme($ancestry, 'Any');
        $this->assertSame([['year' => '2011', 'month' => '7']], $data);

        // findAllYearsAndMonthsByProgramme query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindAllYearsAndMonthsByProgrammeWhenEmptyResultSet()
    {
        $this->loadFixtures([]);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        $entities = $repo->findAllYearsAndMonthsByProgramme([1], 'Any');
        $this->assertEquals([], $entities);

        // findAllYearsAndMonthsByProgramme query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
