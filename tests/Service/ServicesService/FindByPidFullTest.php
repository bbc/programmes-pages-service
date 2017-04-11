<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ServicesService;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidFullTest extends AbstractServicesServiceTest
{
    public function testFindByPidFull()
    {
        $pid = new Pid('s1234567');
        $dbData = new Service(
            'bbc_radio_two',
            's1234567',
            'BBC Radio Two',
            'radio',
            'audio'
        );

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid)
            ->willReturn($dbData);

        $result = $this->service()->findByPidFull($pid);
        $serviceResult = $this->serviceFromDbData($dbData);

        $this->assertEquals($serviceResult->getPid(), $result->getPid());
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
