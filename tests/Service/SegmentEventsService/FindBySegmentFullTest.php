<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentEventsService;

class FindBySegmentFullTest extends AbstractSegmentEventsServiceTest
{
    public function testFindBySegmentFullDefaultPagination()
    {
        $dbId = 1;
        $dbData = [['pid' => 'sg000001']];
        $segment = $this->mockEntity('Segment', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findBySegmentFull')
            ->with([$dbId], true, 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findBySegmentFull($segment, true);
        $this->assertEquals($this->segmentEventsFromDbData($dbData), $result);
    }

    public function testFindBySegmentFullCustomPagination()
    {
        $dbId = 1;
        $dbData = [['pid' => 'sg000001'], ['pid' => 'sg000002'], ['pid' => 'sg000003']];
        $segment = $this->mockEntity('Segment', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findBySegmentFull')
            ->with([$dbId], true, 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findBySegmentFull($segment, true, 5, 3);
        $this->assertEquals($this->segmentEventsFromDbData($dbData), $result);
    }

    public function testFindBySegmentFullWithNonExistentDbId()
    {
        $dbId = 999;
        $segment = $this->mockEntity('Segment', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findBySegmentFull')
            ->with([$dbId], true, 300, 0)
            ->willReturn([]);

        $result = $this->service()->findBySegmentFull($segment, true);
        $this->assertEquals([], $result);
    }
}
