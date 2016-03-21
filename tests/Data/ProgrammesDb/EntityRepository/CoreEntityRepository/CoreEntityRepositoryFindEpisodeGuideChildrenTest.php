<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class CoreEntityRepositoryFindEpisodeGuideChildrenTest extends AbstractDatabaseTest
{
    /**
     * @dataProvider findEpisodeGuideChildrenDataProvider
     */
    public function testFindEpisodeGuideChildren($pid, $limit, $offset, $expectedPids)
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        $id = $repo->findOneByPid($pid)->getId();
        $this->resetDbQueryLogger();

        $entities = $repo->findEpisodeGuideChildren($id, $limit, $offset);
        $this->assertEquals($expectedPids, array_column($entities, 'pid'));

        // findEpisodeGuideChildren query and the parent lookup query
        $this->assertCount(2, $this->getDbQueries());
    }

    public function findEpisodeGuideChildrenDataProvider()
    {

        return [
            ['b010t19z', 50, 0, ['b00tf1zy', 'b010t150', 'b00swyx1']],
            ['b010t19z', 2, 1, ['b010t150', 'b00swyx1']],
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

        $id = $repo->findOneByPid('b010t19z')->getId();
        $this->resetDbQueryLogger();

        $this->assertEquals(3, $repo->countEpisodeGuideChildren($id));

        // count query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
