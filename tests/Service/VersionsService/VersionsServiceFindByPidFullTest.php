<?php

namespace Tests\BBC\ProgrammesPagesService\Service\VersionsService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class VersionsServiceFindByPidFullTest extends AbstractVersionsServiceTest
{
    public function testFindByPidFull()
    {
        $pid = new Pid('b06tl314');
        $dbData = ['pid' => 'b06tl314'];

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid)
            ->willReturn($dbData);

        $result = $this->service()->findByPidFull($pid);
        $this->assertEquals($this->versionFromDbData($dbData), $result);
    }

    public function testFindByPidFullEmptyData()
    {
        $pid = new Pid('b06tl314');

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid)
            ->willReturn(null);

        $result = $this->service()->findByPidFull($pid);
        $this->assertEquals(null, $result);
    }
}
