<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository::<public>
 */
class FindBySegmentBroadcastOrderTest extends AbstractDatabaseTest
{
    public function setUp()
    {
        $this->enableEmbargoedFilter();
    }

    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function findBySegmentData()
    {
        return [
            [['s0000001'], 50, 0, ['sv000004', 'sv000005', 'sv000001', 'sv000006', 'sv000003']], // Test Broadcast Order
        ];
    }

    public function testFindBySegmentOrderByBroadcast()
    {
        $this->loadFixtures(['SegmentEventsBroadcastsOrderFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:SegmentEvent');

        foreach ($this->findBySegmentData() as $data) {
            list($pids, $limit, $offset, $expectedPids) = $data;

            $ids = array_map(function ($dbId) {
                return $this->getDbIdFromPid($dbId, 'Segment');
            }, $pids);

            $entities = $repo->findBySegment($ids, $limit, $offset);

            // Expect embargoed version to be last (ORDER BY hasBroadcast ASC)
            $this->assertEquals($expectedPids, array_column($entities, 'pid'));

            // findBySegment query only
            $this->assertCount(1, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }
}
