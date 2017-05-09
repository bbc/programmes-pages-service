<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ServicesService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidFullTest extends AbstractServicesServiceTest
{
    public function testFindByPidFull()
    {
        $pid = new Pid('s1234567');
        $dbData = ['pid' => 's1234567'];

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid)
            ->willReturn($dbData);

        $result = $this->service()->findByPidFull($pid);

        $this->assertEquals($this->serviceFromDbData($dbData), $result);
    }

    public function testFindByPidFullEmptyData()
    {
        $pid = new Pid('s1234567');

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid)
            ->willReturn(null);

        $result = $this->service()->findByPidFull($pid);
        $this->assertEquals(null, $result);
    }
}
