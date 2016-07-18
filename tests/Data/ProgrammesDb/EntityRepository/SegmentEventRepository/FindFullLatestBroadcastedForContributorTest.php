<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository::<public>
 */
class FindFullLatestBroadcastedForContributorTest extends AbstractDatabaseTest
{
    public function testFindFullLatestBroadcastedForContributor()
    {
        $this->loadFixtures(['SegmentEventsForArtistsFixture']);
        /** @var SegmentEventRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:SegmentEvent');

        // due to my fixture I know that my contributor ID is 2.
        // If the fixtures get more complicated this will need to
        // be updated with a better way of fetching the ID by using
        // either pid: cntrbtr2 or MusicBrainz: 7746d775-9550-4360-b8d5-c37bd448ce01
        $contributorId = 2;

        $segmentEvents = $repo->findFullLatestBroadcastedForContributor(
            $contributorId,
            50,
            0
        );
        $this->assertEquals(2, count($segmentEvents));

        // check that the items are in the right order
        // and that they have the full nesting and hierarchy
        $se1 = $segmentEvents[0];
        $se2 = $segmentEvents[1];

        $this->assertEquals('se000003', $se1['pid']);
        $this->assertEquals('se000002', $se2['pid']);

        $this->assertEquals('sgmntms3', $se1['segment']['pid']);
        $this->assertEquals('sgmntms2', $se2['segment']['pid']);

        $v1 = $se1['version'];
        $v2 = $se2['version'];

        $this->assertEquals('v0000002', $v1['pid']);
        $this->assertEquals('v0000001', $v2['pid']);

        $ep1 = $v1['programmeItem'];
        $ep2 = $v2['programmeItem'];

        $this->assertEquals('b00syxx6', $ep1['pid']);
        $this->assertEquals('b00swgkn', $ep2['pid']);

        // this next bit tests that the ancestry was fetched correctly
        $this->assertEquals('b00swyx1', $ep1['parent']['pid']);
        $this->assertEquals('b00swyx1', $ep2['parent']['pid']);
        $this->assertEquals('b010t19z', $ep1['parent']['parent']['pid']);
        $this->assertEquals('b010t19z', $ep2['parent']['parent']['pid']);

        // Should only be 2 calls.
        // One with all the joins, and one for ancestry
        $this->assertCount(2, $this->getDbQueries());
    }
}
