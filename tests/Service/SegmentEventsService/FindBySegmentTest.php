<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentEventsService;

use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\Entity\SegmentEvent;

class FindBySegmentTest extends AbstractSegmentEventsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testFindBySegmentFullDefaultPagination(int $expectedLimit, int $expectedOffset, array $paramsPagination)
    {
        $segment = $this->createConfiguredMock(Segment::class, ['getDbId' => 1]);

        $this->mockRepository->expects($this->once())
            ->method('findBySegment')
            ->with([$segment->getDbId()], true, $expectedLimit, $expectedOffset);

        $this->service()->findBySegment($segment, true, ...$paramsPagination);
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
        $this->mockRepository->method('findBySegment')->willReturn([['pid' => 'sg000001'], ['pid' => 'sg000002']]);

        $segmentEvents = $this->service()->findBySegment($this->createMock(Segment::class), true);

        $this->assertCount(2, $segmentEvents);
        $this->assertContainsOnly(SegmentEvent::class, $segmentEvents);
        $this->assertEquals('sg000001', $segmentEvents[0]->getPid());
        $this->assertEquals('sg000002', $segmentEvents[1]->getPid());
    }

    public function testEmptyArrayIsReceivedWhenNoResultsFound()
    {
        $this->mockRepository->method('findBySegment')->willReturn([]);

        $result = $this->service()->findBySegment($this->createMock(Segment::class), true);

        $this->assertEquals([], $result);
    }
}
