<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class CoreEntityRepositoryTest extends AbstractDatabaseTest
{
    public function testFindByPidFull()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $entity = $repo->findByPidFull('b00swyx1');
        $this->assertInternalType('array', $entity);
        $this->assertEquals('b00swyx1', $entity['pid']);

        // findByPid query and the parent lookup query
        $this->assertCount(2, $this->getDbQueries());
    }

    public function testFindByPidFullWhenEmptyResult()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $entity = $repo->findByPidFull('qqqqqqq');
        $this->assertNull($entity);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }

    /**
     * @dataProvider findAllWithParentsDataProvider
     */
    public function testFindAllWithParents($limit, $offset, $expectedPids)
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $entities = $repo->findAllWithParents($limit, $offset);
        $this->assertEquals($expectedPids, array_column($entities, 'pid'));

        // findAll query and the parent lookup query
        $this->assertCount(2, $this->getDbQueries());
    }

    public function findAllWithParentsDataProvider()
    {
        return [
            [50, 0, ['b010t19z', 'b00tf1zy', 'b00swgkn', 'b00syxx6', 'b00t0ycf', 'b0175lqm', 'b0176rgj', 'b0177ffr', 'b00swyx1', 'b010t150']],
            [2, 3, ['b00syxx6', 'b00t0ycf']],
        ];
    }

    public function testFindAllWithParentsWhenEmptyResultSet()
    {
        $this->loadFixtures([]);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $entities = $repo->findAllWithParents(50, 0);
        $this->assertEquals([], $entities);

        // findAll query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testCountAll()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $this->assertEquals(10, $repo->countAll());

        // count query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
