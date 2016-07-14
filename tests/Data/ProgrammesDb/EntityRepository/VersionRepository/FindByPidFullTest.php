<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository::<public>
 */
class FindByPidFullTest extends AbstractDatabaseTest
{
    public function setUp()
    {
        $this->enableEmbargoedFilter();
    }

    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function testFindByPidFull()
    {
        $this->loadFixtures(['VersionFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $entity = $repo->findByPidFull('v0000001');
        $this->assertInternalType('array', $entity);
        $this->assertEquals('v0000001', $entity['pid']);
        $this->assertEquals('p0000001', $entity['programmeItem']['pid']);
        $this->assertEquals(['Original', 'Other'], array_column($entity['versionTypes'], 'name'));

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidFullWhenEmptyResult()
    {
        $this->loadFixtures(['VersionFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $entity = $repo->findByPidFull('qqqqqqq');
        $this->assertNull($entity);

        // findByPidFull query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidFullWhenParentIsEmbargoed()
    {
        $this->loadFixtures(['VersionFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');

        $entity = $repo->findByPidFull('v0000002');
        $this->assertNull($entity);

        // findByPidFull query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidFullWhenParentIsEmbargoedAndFilterIsDisabled()
    {
        $this->disableEmbargoedFilter();

        $this->loadFixtures(['VersionFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Version');
        $entity = $repo->findByPidFull('v0000002');

        $this->assertInternalType('array', $entity);
        $this->assertEquals('v0000002', $entity['pid']);
        $this->assertEquals('p0000002', $entity['programmeItem']['pid']);
        $this->assertEquals(['Original', 'Other'], array_column($entity['versionTypes'], 'name'));

        // findByPidFull query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
