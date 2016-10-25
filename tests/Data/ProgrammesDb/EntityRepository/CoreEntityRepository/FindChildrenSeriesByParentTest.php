<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class FindChildrenSeriesByParentTest extends AbstractDatabaseTest
{
    public function testChildrenSeriesByParent()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $dbid = $this->getDbIdFromPid('b010t19z', 'Brand');

        $entities = $repo->findChildrenSeriesByParent($dbid, 50, 0);

        $expectedPids = ['b00swyx1', 'b010t150'];
        $this->assertEquals($expectedPids, array_column($entities, 'pid'));

        // findAll query and the parent lookup query
        $this->assertCount(2, $this->getDbQueries());
    }
}
