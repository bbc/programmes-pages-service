<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentEventsService;

use BBC\ProgrammesPagesService\Domain\Entity\SegmentEvent;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidFullTest extends AbstractSegmentEventsServiceTest
{
    public function testCommunicationWithRepository()
    {
        $pid = $this->createMock(Pid::class);

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid);

        $this->service()->findByPidFull($pid);
    }

    public function testFindByPidFull()
    {
        $this->mockRepository->method('findByPidFull')->willReturn(['pid' => 'sg000001']);

        $segmentEvent = $this->service()->findByPidFull($pid = $this->createMock(Pid::class));

        $this->assertInstanceOf(SegmentEvent::class, $segmentEvent);
        $this->assertEquals('sg000001', $segmentEvent->getPid());
    }

    public function testFindByPidFullEmptyData()
    {
        $this->mockRepository->method('findByPidFull')->willReturn(null);

        $segment = $this->service()->findByPidFull($pid = $this->createMock(Pid::class));

        $this->assertEquals(null, $segment);
    }
}
