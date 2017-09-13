<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ImageRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ImageRepository::<public>
 */
class FindByPidTest extends AbstractDatabaseTest
{
    public function setUp()
    {
        $this->enableEmbargoedFilter();
    }

    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function testFindByPid()
    {
        $this->loadFixtures(['ImagesFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Image');

        $entity = $repo->findByPid('mg000001');
        $this->assertInternalType('array', $entity);
        $this->assertEquals('mg000001', $entity['pid']);

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidWhenEmptyResult()
    {
        $this->loadFixtures(['ImagesFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Image');

        $entity = $repo->findByPid('qqqqqqq');
        $this->assertNull($entity);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidWhenParentIsEmbargoed()
    {
        $this->loadFixtures(['ImagesFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Image');

        $entity = $repo->findByPid('mg000004');
        $this->assertNull($entity);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidWhenParentIsEmbargoedAndFilterIsDisabled()
    {
        $this->disableEmbargoedFilter();

        $this->loadFixtures(['ImagesFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Image');
        $entity = $repo->findByPid('mg000004');

        $this->assertInternalType('array', $entity);
        $this->assertEquals('mg000004', $entity['pid']);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
