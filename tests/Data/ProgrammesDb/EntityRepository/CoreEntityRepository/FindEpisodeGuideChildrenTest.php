<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class FindEpisodeGuideChildrenTest extends AbstractDatabaseTest
{
    public function tearDown()
    {
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

    /**
     * @dataProvider findEpisodeGuideChildrenDataProvider
     */
    public function testFindEpisodeGuideChildren($pid, $limit, $offset, $expectedPids)
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        $id = $this->getCoreEntityDbId($pid);

        $entities = $repo->findEpisodeGuideChildren($id, $limit, $offset);
        $this->assertEquals($expectedPids, array_column($entities, 'pid'));

        // findEpisodeGuideChildren query and the parent lookup query
        $this->assertCount(2, $this->getDbQueries());
    }

    public function findEpisodeGuideChildrenDataProvider()
    {

        return [
            ['b010t19z', 50, 0, ['b006x3cd', 'b00tf1zy', 'b010t150', 'b00swyx1']],
            ['b010t19z', 2, 1, ['b00tf1zy', 'b010t150']],
        ];
    }

    public function testFindEpisodeGuideChildrenWhenEmptyResultSet()
    {
        $this->loadFixtures([]);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $entities = $repo->findEpisodeGuideChildren(1, 50, 0);
        $this->assertEquals([], $entities);

        // findEpisodeGuideChildren query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testCountEpisodeGuideChildren()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $id = $this->getCoreEntityDbId('b010t19z');

        $this->assertEquals(4, $repo->countEpisodeGuideChildren($id));

        // count query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
