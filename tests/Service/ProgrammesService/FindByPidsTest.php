<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidsTest extends AbstractProgrammesServiceTest
{
    /**
     * @dataProvider typesProvider
     */
    public function testCommunicationProtocolWithRepository(string $expectedType, array $specifiedType)
    {
        $pidOne = $this->createMock(Pid::class);
        $pidTwo = $this->createMock(Pid::class);

        $pids = [$pidOne, $pidTwo];

        $this->mockRepository->expects($this->once())
            ->method('findByPids')
            ->with($pids, $expectedType);

        $this->service()->findByPids($pids, ...$specifiedType);
    }

    public function typesProvider(): array
    {
        return [
            'default entity type' => ['Programme', []],
            'custom entity type' => ['ProgrammeContainer', ['ProgrammeContainer']],
        ];
    }

    public function testResultsAreReceivedByService()
    {
        $this->mockRepository->method('findByPids')->willReturn([['pid' => 'b010t19z'], ['pid' => 'b006q2x0']]);

        $programmes = $this->service()->findByPids([$this->createMock(Pid::class)]);

        $this->assertContainsOnly(Programme::class, $programmes);

        $this->assertEquals('b010t19z', $programmes[0]->getPid());
    }

    public function testEmptyArrayIsReceivedWhenNoDbResultsAreFound()
    {
        $this->mockRepository->method('findByPids')->willReturn([]);

        $result = $this->service()->findByPids([$this->createMock(Pid::class)]);

        $this->assertEquals([], $result);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Called findByPids with an invalid type. Expected one of "Programme", "ProgrammeContainer", "ProgrammeItem", "Brand", "Series", "Episode", "Clip" but got "junk"
     */
    public function testFindByPidWithInvalidEntityType()
    {
        $this->service()->findByPids([new Pid('b010t19z')], 'junk');
    }
}
