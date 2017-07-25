<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CoreEntityService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidFullTest extends AbstractCoreEntityServiceTest
{
    public function testFindByPidFull()
    {
        $pid = new Pid('b010t19z');
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid, 'CoreEntity')
            ->willReturn($dbData);

        $result = $this->service()->findByPidFull($pid);
        $this->assertEquals($this->coreEntityFromDbData($dbData), $result);
    }

    public function testFindByPidFullEmptyData()
    {
        $pid = new Pid('b010t19z');

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid)
            ->willReturn(null);

        $result = $this->service()->findByPidFull($pid);
        $this->assertNull($result);
    }
}
