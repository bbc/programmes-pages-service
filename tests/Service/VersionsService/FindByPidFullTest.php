<?php

namespace Tests\BBC\ProgrammesPagesService\Service\VersionsService;

use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidFullTest extends AbstractVersionsServiceTest
{
    public function testServiceCanReceiveSpecifiedVersion()
    {
        $pid = $this->createMock(Pid::class);

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid)
            ->willReturn(['pid' => 'b06tl314']);

        $version = $this->service()->findByPidFull($pid);

        $this->assertInstanceOf(Version::class, $version);
        $this->assertEquals('b06tl314', $version->getPid());
    }

    public function testNullIsReceivedWhenVersionNotFound()
    {
        $pid = $this->createMock(Pid::class);

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid)
            ->willReturn(null);

        $version = $this->service()->findByPidFull($pid);

        $this->assertEquals(null, $version);
    }
}
