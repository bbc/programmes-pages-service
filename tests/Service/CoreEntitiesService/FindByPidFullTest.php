<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CoreEntitiesService;

use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidFullTest extends AbstractCoreEntitiesServiceTest
{
    public function testFindByPidFullInteraction()
    {
        $pid = new Pid('b010t19z');

        $this->mockRepository->expects($this->once())
             ->method('findByPidFull')
             ->with($pid, 'CoreEntity');

        $this->service()->findByPidFull($pid);
    }

    public function testFindByPidFullResult()
    {
        $this->mockRepository->method('findByPidFull')->willReturn(['pid' => 'b010t19z']);

        $coreEntity = $this->service()->findByPidFull(new Pid('b010t19z'));

        $this->assertInstanceOf(CoreEntity::class, $coreEntity);
        $this->assertEquals('b010t19z', (string) $coreEntity->getPid());
    }

    public function testFindByPidFullWithCustomEntityType()
    {
        $pid = new Pid('b010t19z');
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid, 'ProgrammeContainer')
            ->willReturn($dbData);

        $result = $this->service()->findByPidFull($pid, 'ProgrammeContainer');
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
