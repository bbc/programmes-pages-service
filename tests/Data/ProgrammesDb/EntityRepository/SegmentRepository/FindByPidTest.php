<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentRepository;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentRepository::<public>
 */
class FindByPidTest extends AbstractDatabaseTest
{
    public function testFindByPid()
    {
        $this->loadFixtures(['SegmentsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Segment');

        $entity = $repo->findByPid('sgmntms1');
        $this->assertInternalType('array', $entity);
        $this->assertEquals('sgmntms1', $entity['pid']);
        $this->assertEquals('music', $entity['type']);
        $this->assertEquals('Song 1', $entity['title']);

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidWhenEmptyResult()
    {
        $this->loadFixtures(['SegmentsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Segment');

        $entity = $repo->findByPid('qqqqqqq');
        $this->assertNull($entity);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
