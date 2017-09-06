<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ServicesService;

use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidFullTest extends AbstractServicesServiceTest
{
    public function testServiceCommunicationWithRepositoryInterface()
    {
        $pid = $this->createMock(Pid::class);

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid);

        $this->service()->findByPidFull($pid);
    }

    public function testServiceIsReceivedWhenFound()
    {
        $this->mockRepository->method('findByPidFull')->willReturn(['pid' => 's1234567']);

        $service = $this->service()->findByPidFull($this->createMock(Pid::class));

        $this->assertInstanceOf(Service::class, $service);
        $this->assertEquals('s1234567', $service->getPid());
    }

    public function testNullIsReceivedWhenServiceIsNotFound()
    {
        $this->mockRepository->method('findByPidFull')->willReturn(null);

        $result = $this->service()->findByPidFull($this->createMock(Pid::class));

        $this->assertNull(null, $result);
    }
}
