<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidTest extends AbstractProgrammesServiceTest
{
    public function testCommunicationProtocolWithRepository()
    {
        $pid = $this->createMock(Pid::class);

        $this->mockRepository->expects($this->at(0))
            ->method('findByPid')->with($pid, 'Programme');

        $this->mockRepository->expects($this->at(1))
            ->method('findByPid')->with($pid, 'ProgrammeContainer');

        $this->service()->findByPid($pid);
        $this->service()->findByPid($pid, 'ProgrammeContainer');
    }

    public function testResultsAreReceivedByService()
    {
        $this->mockRepository->method('findByPid')->willReturn(['pid' => 'b010t19z']);

        $programme = $this->service()->findByPid($this->createMock(Pid::class));

        $this->assertInstanceOf(Programme::class, $programme);
        $this->assertEquals('b010t19z', $programme->getPid());
    }

    public function testNullIsReceivedWhenNoDbResultsAreFound()
    {
        $this->mockRepository->method('findByPid')->willReturn(null);

        $result = $this->service()->findByPid($this->createMock(Pid::class));

        $this->assertNull($result);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Called findByPid with an invalid type. Expected one of "Programme", "ProgrammeContainer", "ProgrammeItem", "Brand", "Series", "Episode", "Clip" but got "junk"
     */
    public function testFindByPidWithInvalidEntityType()
    {
        $this->service()->findByPid(new Pid('b010t19z'), 'junk');
    }
}
