<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class ProgrammesServiceFindIdByPidTest extends AbstractProgrammesServiceTest
{
    public function testFindIdByPid()
    {
        $pid = new Pid('b010t19z');
        $dbData = 1;

        $this->mockRepository->expects($this->once())
            ->method('findIdByPid')
            ->with($pid)
            ->willReturn($dbData);

        $result = $this->service()->findIdByPid($pid);
        $this->assertSame($dbData, $result);
    }

    public function testFindIdByPidEmptyData()
    {
        $pid = new Pid('qqqqqqqq');

        $this->mockRepository->expects($this->once())
            ->method('findIdByPid')
            ->with($pid)
            ->willReturn(null);

        $result = $this->service()->findIdByPid($pid);
        $this->assertNull($result);
    }
}
