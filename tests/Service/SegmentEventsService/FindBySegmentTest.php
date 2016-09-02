<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentEventsService;

class FindBySegmentTest extends AbstractSegmentEventsServiceTest
{
    public function testFindBySegmentDefaultPagination()
    {
        $dbId = 1;
        $dbData = [['pid' => 'sg000001']];
        $segment = $this->mockEntity('Segment', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findBySegment')
            ->with([$dbId])
            ->willReturn($dbData);

        $result = $this->service()->findBySegment($segment);
        $this->assertEquals($this->segmentEventsFromDbData($dbData), $result);
    }

    public function testFindBySegmentCustomPagination()
    {
        $dbId = 1;
        $dbData = [['pid' => 'sg000001'], ['pid' => 'sg000002'], ['pid' => 'sg000003']];
        $segment = $this->mockEntity('Segment', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findBySegment')
            ->with([$dbId], 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findBySegment($segment, 5, 10);
        $this->assertEquals($this->segmentEventsFromDbData($dbData), $result);
    }

    public function testFindBySegmentWithNonExistentDbId()
    {
        $dbId = 999;
        $segment = $this->mockEntity('Segment', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findBySegment')
            ->with([$dbId])
            ->willReturn([]);

        $result = $this->service()->findBySegment($segment);
        $this->assertEquals([], $result);
    }
}
