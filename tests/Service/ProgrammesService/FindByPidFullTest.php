<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;


class FindByPidFullTest extends AbstractProgrammesServiceTest
{
    /**
     * @dataProvider entityTypeParamProvider
     */
    public function testCommunicationProtocolWithRepository(string $expectedEntityType, array $entityTypeProvided)
    {
        $pid = $this->createMock(Pid::class);

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($pid, $expectedEntityType);

        $this->service()->findByPidFull($pid, ...$entityTypeProvided);
    }

    public function entityTypeParamProvider()
    {
        return [
            'CASE: default type' => ['Programme', []],
            'CASE: custom type' => ['ProgrammeContainer', ['ProgrammeContainer']],
        ];
    }

    public function testResultsAreReceivedByService()
    {
        $this->mockRepository->method('findByPidFull')->willReturn(['pid' => 'b010t19z']);

        $programme = $this->service()->findByPidFull($this->createMock(Pid::class));

        $this->assertInstanceOf(Programme::class, $programme);
        $this->assertEquals('b010t19z', $programme->getPid());
    }

    public function testNullIsReceivedWhenNoDbResultsAreFound()
    {
        $this->mockRepository->method('findByPidFull')->willReturn(null);

        $programme = $this->service()->findByPidFull($this->createMock(Pid::class));

        $this->assertNull($programme);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Called findByPidFull with an invalid type. Expected one of "Programme", "ProgrammeContainer", "ProgrammeItem", "Brand", "Series", "Episode", "Clip" but got "junk"
     */
    public function testFindByPidFullWithInvalidEntityType()
    {
        $this->service()->findByPidFull(new Pid('b010t19z'), 'junk');
    }
}
