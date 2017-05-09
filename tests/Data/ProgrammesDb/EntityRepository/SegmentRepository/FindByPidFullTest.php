<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentRepository::<public>
 */
class FindByPidFullTest extends AbstractDatabaseTest
{
    public function testFindByPidFull()
    {
        $this->loadFixtures(['SegmentsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Segment');

        $entity = $repo->findByPidFull('sgmntms1');
        $this->assertInternalType('array', $entity);
        $this->assertEquals('sgmntms1', $entity['pid']);
        $this->assertEquals('music', $entity['type']);
        $this->assertEquals('Song 1', $entity['title']);

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidFullWhenEmptyResult()
    {
        $this->loadFixtures(['SegmentsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Segment');

        $entity = $repo->findByPidFull('qqqqqqq');
        $this->assertNull($entity);

        // findByPidFull query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
