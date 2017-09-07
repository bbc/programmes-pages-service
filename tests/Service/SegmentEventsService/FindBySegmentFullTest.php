<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentEventsService;

use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\Entity\SegmentEvent;

class FindBySegmentFullTest extends AbstractSegmentEventsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testCommunicationWithRepository(int $expectedLimit, int $expectedOffset, array $paramsPagination)
    {
        $segment = $this->createConfiguredMock(Segment::class, ['getDbId' => 1]);

        $this->mockRepository->expects($this->once())
            ->method('findBySegmentFull')
            ->with([$segment->getDbId()], true, $expectedLimit, $expectedOffset);

        $this->service()->findBySegmentFull($segment, true, ...$paramsPagination);
    }

    public function paginationProvider(): array
    {
        return [
            // expected limit, expected offset, user pagination params
            'CASE: default pagination' => [300, 0, []],
            'CASE: custom pagination' => [3, 12, [3, 5]],
        ];
    }

    public function testArrayOfSegmentsEventsAreReceivedWhenFoundResults()
    {
        $this->mockRepository->method('findBySegmentFull')->willReturn([['pid' => 'sg000001'], ['pid' => 'sg000002']]);

        $segmentEvents = $this->service()->findBySegmentFull($this->createMock(Segment::class), true);

        $this->assertCount(2, $segmentEvents);
        $this->assertContainsOnly(SegmentEvent::class, $segmentEvents);
        $this->assertEquals('sg000001', $segmentEvents[0]->getPid());
        $this->assertEquals('sg000002', $segmentEvents[1]->getPid());
    }

    public function testEmptyArrayIsReceivedWhenNoFoundResults()
    {
        $this->mockRepository->method('findBySegmentFull')->willReturn([]);

        $segmentEvents = $this->service()->findBySegmentFull($this->createMock(Segment::class, true));

        $this->assertEquals([], $segmentEvents);
    }
}
