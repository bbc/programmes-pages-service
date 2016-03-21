<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class ProgrammesServiceFindByPidTest extends AbstractProgrammesServiceTest
{
    public function testFindByPid()
    {
        $pid = new Pid('b010t19z');
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($this->equalTo($pid))
            ->willReturn($dbData);

        $result = $this->programmesService()->findByPidFull($pid);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindByPidEmptyData()
    {
        $pid = new Pid('b010t19z');

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($this->equalTo($pid))
            ->willReturn(null);

        $result = $this->programmesService()->findByPidFull($pid);
        $this->assertEquals(null, $result);
    }
}
