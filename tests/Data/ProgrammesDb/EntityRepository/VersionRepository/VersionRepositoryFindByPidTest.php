<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository::<public>
 */
class VersionRepositoryFindByPidTest extends AbstractDatabaseTest
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
        $this->loadFixtures(['VersionFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $entity = $repo->findByPid('v0000001');
        $this->assertInternalType('array', $entity);
        $this->assertEquals('v0000001', $entity['pid']);

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidWhenEmptyResult()
    {
        $this->loadFixtures(['VersionFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $entity = $repo->findByPid('qqqqqqq');
        $this->assertNull($entity);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidWhenParentIsEmbargoed()
    {
        $this->loadFixtures(['VersionFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $entity = $repo->findByPid('v0000002');
        $this->assertNull($entity);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidWhenParentIsEmbargoedAndFilterIsDisabled()
    {
        $this->disableEmbargoedFilter();

        $this->loadFixtures(['VersionFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');
        $entity = $repo->findByPid('v0000002');

        $this->assertInternalType('array', $entity);
        $this->assertEquals('v0000002', $entity['pid']);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
