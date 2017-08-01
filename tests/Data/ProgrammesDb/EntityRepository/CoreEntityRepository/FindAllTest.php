<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class FindAllTest extends AbstractDatabaseTest
{
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
            [50, 0, ['b010t19z', 'p00h64pq', 'p00hv9yz', 'p008k0l5', 'p008k0jy', 'p008nhl4', 'b00tf1zy', 'b00swgkn', 'b00syxx6', 'b00t0ycf', 'b0175lqm', 'b0176rgj', 'b0177ffr', 'p008nhl5', 'p008nhl6',  'b00swyx1', 'b010t150']],
            [2, 3, ['p008k0l5', 'p008k0jy']],
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

        $this->assertEquals(17, $repo->countAll());

        // count query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
