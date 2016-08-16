<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentEventsService;

class FindFullLatestBroadcastedForContributorTest extends AbstractSegmentEventsServiceTest
{
    public function testFindLatestBroadcastedForContributor()
    {
        $dbData = [['pid' => 'c000001']];
        $contributor = $this->mockEntity('Contributor', 1);

        $this->mockRepository->expects($this->once())
            ->method('findFullLatestBroadcastedForContributor')
            ->with(1)
            ->willReturn($dbData);

        $result = $this->service()->findLatestBroadcastedForContributor($contributor);
        $this->assertEquals($this->segmentEventsFromDbData($dbData), $result);
    }

    public function testFindLatestBroadcastedForContributorCustomPagination()
    {
        $dbId = 1;
        $dbData = [['pid' => 'c000001'], ['pid' => 'c000002'], ['pid' => 'c000003']];
        $contributor = $this->mockEntity('Contributor', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findFullLatestBroadcastedForContributor')
            ->with($dbId, 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findLatestBroadcastedForContributor($contributor, 5, 3);
        $this->assertEquals($this->segmentEventsFromDbData($dbData), $result);
    }

    public function testFindLatestBroadcastedForContributorWithNonExistentDbId()
    {
        $dbId = 999;
        $contributor = $this->mockEntity('Contributor', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findFullLatestBroadcastedForContributor')
            ->with($dbId, 5, 10)
            ->willReturn([]);

        $result = $this->service()->findLatestBroadcastedForContributor($contributor, 5, 3);
        $this->assertEquals([], $result);
    }
}
