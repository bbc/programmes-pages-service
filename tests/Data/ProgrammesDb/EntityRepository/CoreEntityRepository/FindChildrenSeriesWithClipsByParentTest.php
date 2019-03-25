<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class FindChildrenSeriesWithClipsByParentTest extends AbstractDatabaseTest
{
    public function tearDown()
    {
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

    public function testChildrenSeriesWithClipsByParent()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $dbid = $this->getDbIdFromPersistentIdentifier('b010t19z', 'Brand');

        $entities = $repo->findChildrenSeriesWithClipsByParent($dbid, 50, 0, false);

        $expectedPids = ['b00swyx1', 'b010t150'];
        $this->assertEquals($expectedPids, array_column($entities, 'pid'));

        $this->assertCount(2, $this->getDbQueries());
    }

    public function testChildrenSeriesWithClipsByParentWithLimit()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $dbid = $this->getDbIdFromPersistentIdentifier('b010t19z', 'Brand');

        $entities = $repo->findChildrenSeriesWithClipsByParent($dbid, 1, 0, false);

        $expectedPids = ['b00swyx1'];
        $this->assertEquals($expectedPids, array_column($entities, 'pid'));

        $this->assertCount(2, $this->getDbQueries());
    }

    public function testChildrenSeriesByParentWithClipsWithPage()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $dbid = $this->getDbIdFromPersistentIdentifier('b010t19z', 'Brand');

        $entities = $repo->findChildrenSeriesWithClipsByParent($dbid, 50, 1, false);

        $expectedPids = ['b010t150'];
        $this->assertEquals($expectedPids, array_column($entities, 'pid'));

        $this->assertCount(2, $this->getDbQueries());
    }
}
