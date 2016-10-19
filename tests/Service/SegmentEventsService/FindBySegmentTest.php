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
                             ->with([$dbId], true, 300, 0)
                             ->willReturn($dbData);

        $result = $this->service()->findBySegment($segment, true);
        $this->assertEquals($this->segmentEventsFromDbData($dbData), $result);
    }

    public function testFindBySegmentCustomPagination()
    {
        $dbId = 1;
        $dbData = [['pid' => 'sg000001'], ['pid' => 'sg000002'], ['pid' => 'sg000003']];
        $segment = $this->mockEntity('Segment', $dbId);

        $this->mockRepository->expects($this->once())
                             ->method('findBySegment')
                             ->with([$dbId], true, 5, 10)
                             ->willReturn($dbData);

        $result = $this->service()->findBySegment($segment, true, 5, 3);
        $this->assertEquals($this->segmentEventsFromDbData($dbData), $result);
    }

    public function testFindBySegmentWithNonExistentDbId()
    {
        $dbId = 999;
        $segment = $this->mockEntity('Segment', $dbId);

        $this->mockRepository->expects($this->once())
                             ->method('findBySegment')
                             ->with([$dbId], true, 300, 0)
                             ->willReturn([]);

        $result = $this->service()->findBySegment($segment, true);
        $this->assertEquals([], $result);
    }
}
