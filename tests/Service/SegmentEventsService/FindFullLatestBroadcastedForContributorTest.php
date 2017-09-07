<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentEventsService;

use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\Entity\SegmentEvent;

class FindFullLatestBroadcastedForContributorTest extends AbstractSegmentEventsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testCommunicationWithRepository(int $expectedLimit, int $expectedOffset, array $paramsPagination)
    {
        $contributor = $this->createConfiguredMock(Contributor::class, ['getDbId' => 1]);

        $this->mockRepository->expects($this->once())
            ->method('findFullLatestBroadcastedForContributor')
            ->with($contributor->getDbId(), $expectedLimit, $expectedOffset);

        $this->service()->findLatestBroadcastedForContributor($contributor, ...$paramsPagination);
    }

    public function paginationProvider(): array
    {
        return [
            // expected limit, expected offset, user pagination params
            'CASE: default pagination' => [300, 0, []],
            'CASE: custom pagination' => [3, 12, [3, 5]],
        ];
    }


    public function testArrayOfSegmentEventIsReceivedWhenResultsFound()
    {
        $this->mockRepository->method('findFullLatestBroadcastedForContributor')->willReturn([['pid' => 'sg000001'], ['pid' => 'sg000002']]);

        $segmentEvents = $this->service()->findLatestBroadcastedForContributor($this->createMock(Contributor::class));

        $this->assertCount(2, $segmentEvents);
        $this->assertContainsOnly(SegmentEvent::class, $segmentEvents);
        $this->assertEquals('sg000001', $segmentEvents[0]->getPid());
        $this->assertEquals('sg000002', $segmentEvents[1]->getPid());
    }

    public function testEmptyArrayIsReceivedWhenNoResultsFound()
    {
        $this->mockRepository->method('findFullLatestBroadcastedForContributor')->willReturn([]);

        $result = $this->service()->findLatestBroadcastedForContributor($this->createMock(Contributor::class));

        $this->assertEquals([], $result);
    }
}
