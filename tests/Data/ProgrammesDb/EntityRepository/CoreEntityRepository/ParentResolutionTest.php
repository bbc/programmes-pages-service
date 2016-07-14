<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<!public>
 */
class ParentResolutionTest extends AbstractDatabaseTest
{
    public function testSingleEntityRequestWhereNoRootEntityFound()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $entity = $repo->findByPidFull('qqqqqqq');

        $this->assertNull($entity);

        // Ensure only one query - the original findByPid - is made
        $this->assertCount(1, $this->getDbQueries());
    }

    /**
     * Expected Parent Tree: Mongrels Brand (b010t19z)
     */
    public function testSingleEntityRequestWithNoParentInHierarchy()
    {

        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $entity = $repo->findByPidFull('b010t19z');

        // Assert current entity has no parent (it is top of the hierarchy)
        $this->assertArrayNotHasKey('parent', $entity);

        // Ensure only one query - the original findByPid - is made
        $this->assertCount(1, $this->getDbQueries());
    }

    /**
     * Expected Parent Tree: Series 1 (b00swyx1) -> Mongrels Brand (b010t19z)
     */
    public function testSingleEntityRequestWithOneParentInHierarchy()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        // This is Series 1
        $entity = $repo->findByPidFull('b00swyx1');

        // Assert parent is the brand
        $this->assertEquals('b010t19z', $entity['parent']['pid']);
        // Assert parent has no parent (it is top of the hierarchy)
        $this->assertArrayNotHasKey('parent', $entity['parent']);

        // Ensure only two queries - the original findByPid and the parent lookup
        $this->assertCount(2, $this->getDbQueries());
    }

    /**
     * Expected Parent Tree: Episode 1 (b00swgkn) -> Series 1 (b00swyx1) -> Mongrels Brand (b010t19z)
     */
    public function testSingleEntityRequestWithGrandParentsInHierarchy()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        // This is Episode 1
        $entity = $repo->findByPidFull('b00swgkn');

        // Assert parent is the series
        $this->assertEquals('b00swyx1', $entity['parent']['pid']);
        // Assert grandparent is the brand
        $this->assertEquals('b010t19z', $entity['parent']['parent']['pid']);
        // Assert grandparent has no parent (it is top of the hierarchy)
        $this->assertArrayNotHasKey('parent', $entity['parent']['parent']);

        // Ensure only two queries - the original findByPid and the parent lookup
        $this->assertCount(2, $this->getDbQueries());
    }

    public function testMultipleEntityRequest()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        // This is Episode 1
        $entities = $repo->findAllWithParents(50, 0);

        $brand = $this->findEntityByPid($entities, 'b010t19z');
        $series = $this->findEntityByPid($entities, 'b00swyx1');
        $episode = $this->findEntityByPid($entities, 'b00swgkn');

        // Assert hierarchy for Brand
        $this->assertArrayNotHasKey('parent', $brand);

        // Assert hierarchy for Series
        // Assert parent is the brand
        $this->assertEquals('b010t19z', $series['parent']['pid']);
        // Assert parent has no parent (it is top of the hierarchy)
        $this->assertArrayNotHasKey('parent', $series['parent']);

        // Assert hierarchy for Episode
        // Assert parent is the series
        $this->assertEquals('b00swyx1', $episode['parent']['pid']);
        // Assert grandparent is the brand
        $this->assertEquals('b010t19z', $episode['parent']['parent']['pid']);
        // Assert grandparent has no parent (it is top of the hierarchy)
        $this->assertArrayNotHasKey('parent', $episode['parent']['parent']);



        // Ensure only two queries - the original findAll and the parent lookup
        $this->assertCount(2, $this->getDbQueries());
    }

    public function findEntityByPid($entities, $pid)
    {
        foreach ($entities as $entity) {
            if ($entity['pid'] == $pid) {
                return $entity;
            }
        }

        return null;
    }
}
