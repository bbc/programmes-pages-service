<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class FindByPidTest extends AbstractDatabaseTest
{
    public function testFindByPid()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $entity = $repo->findByPid('b00swyx1');
        $this->assertInternalType('array', $entity);
        $this->assertSame('b00swyx1', $entity['pid']);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidFilteringByEntityType()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        // b00swyx1 is a Series so it should be returned when filtering by Series
        $entity = $repo->findByPid('b00swyx1', 'Series');
        $this->assertInternalType('array', $entity);
        $this->assertSame('b00swyx1', $entity['pid']);

        // b00swyx1 is a Series so it should not be returned when filtering by Episodes
        $entity = $repo->findByPid('b00swyx1', 'Episode');
        $this->assertNull($entity);

        // two findByPid queries only
        $this->assertCount(2, $this->getDbQueries());
    }

    public function testFindByPidWhenEmptyResult()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $entity = $repo->findByPid('qqqqqqq');
        $this->assertNull($entity);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Called findByPid with an invalid type. Expected one of "Programme", "ProgrammeContainer", "ProgrammeItem", "Brand", "Series", "Episode", "Clip", "Group", "Collection", "Gallery", "Season", "Franchise", "CoreEntity" but got "junk"
     */
    public function testFindByPidWithInvalidEntityType()
    {
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        $repo->findByPid('qqqqqqq', 'junk');
    }
}
