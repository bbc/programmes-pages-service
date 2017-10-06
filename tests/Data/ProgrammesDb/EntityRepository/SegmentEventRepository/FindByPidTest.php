<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository::<public>
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
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

    public function testFindByPid()
    {
        $this->loadFixtures(['SegmentEventFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:SegmentEvent');

        $entity = $repo->findByPid('sv000001');
        $this->assertInternalType('array', $entity);

        $this->assertEquals('sv000001', $entity['pid']);
        $this->assertEquals('v0000001', $entity['version']['pid']);
        $this->assertEquals('s0000001', $entity['segment']['pid']);

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidWhenEmptyResult()
    {
        $this->loadFixtures(['SegmentEventFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:SegmentEvent');

        $entity = $repo->findByPid('qqqqqqq');
        $this->assertNull($entity);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidWhenParentIsEmbargoed()
    {
        $this->loadFixtures(['SegmentEventFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:SegmentEvent');

        $entity = $repo->findByPid('sv000002');
        $this->assertNull($entity);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidWhenParentIsEmbargoedAndFilterIsDisabled()
    {
        $this->disableEmbargoedFilter();

        $this->loadFixtures(['SegmentEventFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:SegmentEvent');
        $entity = $repo->findByPid('sv000002');

        $this->assertInternalType('array', $entity);
        $this->assertEquals('sv000002', $entity['pid']);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
