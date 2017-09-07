<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidTest extends AbstractProgrammesServiceTest
{
    /**
     * @dataProvider entityTypeParamProvider
     */
    public function testCommunicationProtocolWithRepository(string $expectedEntityType, array $entityTypeProvided)
    {
        $pid = $this->createMock(Pid::class);

        $this->mockRepository->expects($this->once())
            ->method('findByPid')
            ->with($pid, $expectedEntityType);

        $this->service()->findByPid($pid, ...$entityTypeProvided);
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
