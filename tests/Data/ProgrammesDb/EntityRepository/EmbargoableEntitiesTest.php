<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

/**
 * @coversNone
 */
class EmbargoableEntitiesTest extends AbstractDatabaseTest
{
    public function setUp()
    {
        $this->enableEmbargoedFilter();
    }

    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function testEmbargoedCoreEntitiesAreFilteredOut()
    {
        $this->loadFixtures(['EmbargoedProgrammeFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $this->assertEquals(3, $repo->countAll('Programme'));

        $expectedPids = ['b017j7vs', 'b01777fr', 'b017j5jw'];

        $entities = $repo->findAllWithParents(10, 0);
        $this->assertEquals($expectedPids, array_column($entities, 'pid'));
    }
}
