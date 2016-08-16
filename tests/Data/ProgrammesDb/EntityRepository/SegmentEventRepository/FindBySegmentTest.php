<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository::<public>
 */
class FindBySegmentTest extends AbstractDatabaseTest
{
    public function setUp()
    {
        $this->enableEmbargoedFilter();
    }

    public function tearDown()
    {
        $this->disableEmbargoedFilter();
    }

    public function testFindBySegment()
    {
        $this->loadFixtures(['SegmentEventFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:SegmentEvent');

        foreach ($this->findBySegmentData() as $data) {
            list($pids, $limit, $offset, $expectedPids) = $data;

            $ids = array_map(function ($dbId) {
                return $this->getDbIdFromPid($dbId, 'Segment');
            }, $pids);

            $entities = $repo->findBySegment($ids, $limit, $offset);
            $this->assertEquals($expectedPids, array_column($entities, 'pid'));

            // findBySegment query only
            $this->assertCount(1, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }

    public function findBySegmentData()
    {
        return [
            [['s0000001'], 50, 0, ['sv000001', 'sv000003', 'sv000004', 'sv000005']], // Implicitly testing enabled embargoed filter
            [['s0000001'], 2, 1, ['sv000003', 'sv000004']],
            [['s0000002'], 50, 0, ['sv000007', 'sv000008']], // Test Distinct Version
        ];
    }

    public function testFindByContributionToWhenEmptyResultSet()
    {
        $this->loadFixtures([]);
        /** @var SegmentEventRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:SegmentEvent');

        $entities = $repo->findBySegment([1], 50, 0);
        $this->assertEquals([], $entities);

        // findByContributionTo query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindBySegmentWhenVersionIsEmbargoedAndFilterIsDisabled()
    {
        $this->disableEmbargoedFilter();

        $this->loadFixtures(['SegmentEventFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:SegmentEvent');
        $expectedPids = ['sv00009', 'sv000011', 'sv000010'];

        $dbId = $this->getDbIdFromPid('s0000003', 'Segment');
        $entities = $repo->findBySegment([$dbId], 50, 0);

        // Expect embargoed version to be last (ORDER BY hasBroadcast ASC)
        $this->assertEquals($expectedPids, array_column($entities, 'pid'));

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindBySegmentOrderByBroadcast()
    {
        $this->loadFixtures(['SegmentEventsBroadcastsOrderFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:SegmentEvent');

        // Specific case where we load up a different fixture so we have to define the data here
        $findBySegmentData = [
            [['s0000001'], 50, 0, ['sv000004', 'sv000005', 'sv000001', 'sv000006', 'sv000003']],
        ]; // Test Broadcast Order

        foreach ($findBySegmentData as $data) {
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
