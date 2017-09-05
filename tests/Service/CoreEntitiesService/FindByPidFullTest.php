<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CoreEntitiesService;

use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidFullTest extends AbstractCoreEntitiesServiceTest
{
    /**
     * @dataProvider entityTypeParamProvider
     */
    public function testComunicationWithDbInterface($expectedEntityType, array $paramEntityType)
    {
        $pid = new Pid('b010t19z');

        $this->mockRepository->expects($this->once())
             ->method('findByPidFull')
             ->with($pid, $expectedEntityType);

        $this->service()->findByPidFull($pid, ...$paramEntityType);
    }

    public function entityTypeParamProvider(): array
    {
        return [
            'CASE: default entity type when no indicated' => ['CoreEntity', []],
            'CASE: explicit entity type' => ['ProgrammeContainer', ['ProgrammeContainer']],
        ];
    }

    /**
     * @dataProvider entityTypeSelectorProvider
     */
    public function testCoreEntityCanBeReceived(string $entityTypeProvided)
    {
        $this->mockRepository->method('findByPidFull')->willReturn(['pid' => 'b010t19z']);

        $coreEntity = $this->service()->findByPidFull(new Pid('b010t19z'), $entityTypeProvided);

        // we cannot be sure that the type returned is a coreEntity or ProgrammeContainer, that is
        // responssibility of the CoreEntitymapper and Repository. But we can test the PID of it
        $this->assertInstanceOf(CoreEntity::class, $coreEntity);
        $this->assertEquals('b010t19z', (string) $coreEntity->getPid());
    }

    public function entityTypeSelectorProvider(): array
    {
        return [
            'CASE: CoreEntity' => ['CoreEntity'],
            'CASE: ProgrammeContainer' => ['ProgrammeContainer'],
        ];
    }

    public function testNullValueIsReceivedWhenNoResults()
    {
        $this->mockRepository->method('findByPidFull')->willReturn(null);

        $coreEntity = $this->service()->findByPidFull(new Pid('b010t19z'));

        $this->assertNull($coreEntity);
    }
}
