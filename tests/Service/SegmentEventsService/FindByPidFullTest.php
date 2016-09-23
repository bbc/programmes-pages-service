<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentEventsService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidFullTest extends AbstractSegmentEventsServiceTest
{
    public function testFindByPidFull()
    {
        $pid = new Pid('sg000001');
        $dbData = ['pid' => 'sg000001'];

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid)
            ->willReturn($dbData);

        $result = $this->service()->findByPidFull($pid);
        $this->assertEquals($this->segmentEventFromDbData($dbData), $result);
    }

    public function testFindByPidFullEmptyData()
    {
        $pid = new Pid('sg000001');

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid)
            ->willReturn(null);

        $result = $this->service()->findByPidFull($pid);
        $this->assertEquals(null, $result);
    }
}
