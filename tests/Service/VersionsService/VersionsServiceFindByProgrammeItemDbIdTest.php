<?php

namespace Tests\BBC\ProgrammesPagesService\Service\VersionsService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class VersionsServiceFindByProgrammeItemDbIdTest extends AbstractVersionsServiceTest
{
    public function testFindByProgrammeItemDbId()
    {
        $dbId = 101;

        $dbData = [['pid' => 'b06tl314'], ['pid' => 'b06ts0v9']];

        $this->mockRepository->expects($this->once())
            ->method('findByProgrammeItem')
            ->with($dbId)
            ->willReturn($dbData);

        $result = $this->service()->findByProgrammeItemDbId($dbId);
        $this->assertEquals($this->versionsFromDbData($dbData), $result);
    }


    public function testFindByProgrammeItemDbIdWithNonExistantPid()
    {
        $dbId = 999;

        $this->mockRepository->expects($this->once())
            ->method('findByProgrammeItem')
            ->with($dbId)
            ->willReturn([]);

        $result = $this->service()->findByProgrammeItemDbId($dbId);
        $this->assertEquals([], $result);
    }
}
