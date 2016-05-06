<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository::<public>
 */
class SegmentEventRepositoryFindByPidTest extends AbstractDatabaseTest
{
    public function testFindByPid()
    {
        $this->loadFixtures(['SegmentEventFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:SegmentEvent');

        $entity = $repo->findByPid('se000001');
        $this->assertInternalType('array', $entity);

        $this->assertEquals('se000001', $entity['pid']);
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

        $entity = $repo->findByPid('se000002');
        $this->assertNull($entity);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
