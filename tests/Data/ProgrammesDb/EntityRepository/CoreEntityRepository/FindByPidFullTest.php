<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class FindByPidFullTest extends AbstractDatabaseTest
{
    public function tearDown()
    {
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

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

    public function testFindByPidFullFilteringByEntityType()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        // b00swyx1 is a Series so it should be returned when filtering by Series
        $entity = $repo->findByPidFull('b00swyx1', 'Series');
        $this->assertInternalType('array', $entity);
        $this->assertSame('b00swyx1', $entity['pid']);

        // b00swyx1 is a Series so it should not be returned when filtering by Episodes
        $entity = $repo->findByPidFull('b00swyx1', 'Episode');
        $this->assertNull($entity);

        // three findByPid queries - query and parent lookup, then the empty result set
        $this->assertCount(3, $this->getDbQueries());
    }

    public function testFindByPidFullData()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $entity = $repo->findByPidFull('b00swgkn');
        $this->assertInternalType('array', $entity);
        $this->assertEquals('b00swgkn', $entity['pid']);
        $this->assertEquals(['ms1', 'ms2'], $entity['downloadableMediaSets']);
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
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Called findByPidFullCommon with an invalid type. Expected one of "CoreEntity", "Programme", "ProgrammeContainer", "ProgrammeItem", "Brand", "Series", "Episode", "Clip", "Group", "Collection", "Gallery", "Season", "Franchise" but got "junk"
     */
    public function testFindByPidWithInvalidEntityType()
    {
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        $repo->findByPidFull('qqqqqqq', 'junk');
    }
}
