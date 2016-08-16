<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentsService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidTest extends AbstractSegmentsServiceTest
{
    public function testFindByPid()
    {
        $pid = new Pid('s1234567');
        $dbData = ['pid' => 's1234567'];

        $this->mockRepository->expects($this->once())
            ->method('findByPid')
            ->with($pid)
            ->willReturn($dbData);

        $result = $this->service()->findByPid($pid);
        $this->assertEquals($this->segmentFromDbData($dbData), $result);
    }

    public function testFindByPidEmptyData()
    {
        $pid = new Pid('s1234567');

        $this->mockRepository->expects($this->once())
            ->method('findByPid')
            ->with($pid)
            ->willReturn(null);

        $result = $this->service()->findByPid($pid);
        $this->assertEquals(null, $result);
    }
}
