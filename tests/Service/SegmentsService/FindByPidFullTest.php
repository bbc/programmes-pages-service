<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentsService;

use BBC\ProgrammesPagesService\Domain\Entity\Segment;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidFullTest extends AbstractSegmentsServiceTest
{
    public function testCommunicationWithRepository()
    {
        $pid = new Pid('s1234567');

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid);

        $this->service()->findByPidFull($pid);
    }

    public function testResultsFoundaaa()
    {
        $this->mockRepository->method('findByPidFull')->willReturn(['pid' => 's1234567']);

        $segment = $this->service()->findByPidFull($this->createMock(Pid::class));

        $this->assertInstanceOf(Segment::class, $segment);
        $this->assertEquals('s1234567', $segment->getPid());
    }


    public function testFindByPidFullEmptyData()
    {
        $this->mockRepository->method('findByPidFull')->willReturn(null);

        $segment = $this->service()->findByPidFull($this->createMock(Pid::class));

        $this->assertNull($segment);
    }
}
