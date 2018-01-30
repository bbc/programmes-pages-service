<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class FindByPidsTest extends AbstractDatabaseTest
{
    public function tearDown()
    {
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

    public function testFindByPids()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $pids = ['b00tf1zy', 'b010t150', 'b00swyx1'];

        $entity = $repo->findByPids($pids);
        $this->assertInternalType('array', $entity);
        $this->assertSame($pids, array_column($entity, 'pid'));

        // findByPids query and parent lookup query only
        $this->assertCount(2, $this->getDbQueries());

        // A similar query but with a different order. This asserts that the
        // entities that are returned are in the same order as the $pids array
        $pids = ['b010t150', 'b00swyx1', 'b00tf1zy'];

        $entity = $repo->findByPids($pids);
        $this->assertInternalType('array', $entity);
        $this->assertSame($pids, array_column($entity, 'pid'));
    }

    public function testFindByPidsFilteringByEntityType()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        // b00swyx1 is a Series so it should be returned when filtering by Series
        $entity = $repo->findByPids(['b00swyx1'], 'Series');
        $this->assertInternalType('array', $entity);
        $this->assertSame('b00swyx1', $entity[0]['pid']);

        // b00swyx1 is a Series so it should not be returned when filtering by Episodes
        $entity = $repo->findByPids(['b00swyx1'], 'Episode');
        $this->assertEquals([], $entity);

        // two findByPids queries and 1 parent lookup only
        $this->assertCount(3, $this->getDbQueries());
    }

    public function testFindByPidsWhenEmptyResult()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $entity = $repo->findByPids(['qqqqqqq']);

        $this->assertEquals([], $entity);

        $this->assertCount(1, $this->getDbQueries());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Called findByPids with an invalid type. Expected one of "CoreEntity", "Programme", "ProgrammeContainer", "ProgrammeItem", "Brand", "Series", "Episode", "Clip", "Group", "Collection", "Gallery", "Season", "Franchise" but got "junk"
     */
    public function testFindByPidWithInvalidEntityType()
    {
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        $repo->findByPids(['qqqqqqq'], 'junk');
    }
}
