<?php

namespace Tests\BBC\ProgrammesPagesService\Service\VersionsService;

use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidFullTest extends AbstractVersionsServiceTest
{
    public function testRepositoryReceiveProperParams()
    {
        $pid = $this->createMock(Pid::class);

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid);

        $this->service()->findByPidFull($pid);
    }

    public function testServiceCanReceiveSpecifiedVersion()
    {
        $this->mockRepository->method('findByPidFull')->willReturn(['pid' => 'b06tl314']);

        $version = $this->service()->findByPidFull($this->createMock(Pid::class));

        $this->assertInstanceOf(Version::class, $version);
        $this->assertEquals('b06tl314', $version->getPid());
    }

    public function testNullIsReceivedWhenVersionNotFound()
    {
        $this->mockRepository->method('findByPidFull')->willReturn(null);

        $version = $this->service()->findByPidFull($this->createMock(Pid::class));

        $this->assertEquals(null, $version);
    }
}
