<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\EntitySingleServiceResult;

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

        $expectedResult = new EntitySingleServiceResult($this->programmeFromDbData($dbData));

        $result = $this->programmesService()->findByPidFull($pid);
        $this->assertEquals($expectedResult, $result);
    }

    public function testFindByPidEmptyData()
    {
        $pid = new Pid('b010t19z');

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($this->equalTo($pid))
            ->willReturn(null);

        $expectedResult = new EntitySingleServiceResult(null);

        $result = $this->programmesService()->findByPidFull($pid);
        $this->assertEquals($expectedResult, $result);
    }
}
