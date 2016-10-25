<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeContainer;

class FindChildrenSeriesByParentTest extends AbstractProgrammesServiceTest
{
    public function testFindAllSeriesByParent()
    {
        $container = $this->createMock(ProgrammeContainer::class);
        $container->method('getDbId')->willReturn(0);

        $dbData = [['pid' => 'b010t19z'], ['pid' => 'b00swyx1']];

        $this->mockRepository->expects($this->once())
            ->method('findChildrenSeriesByParent')
            ->with(0)
            ->willReturn($dbData);

        $result = $this->service()->findChildrenSeriesByParent($container);
        $this->assertEquals($this->programmesFromDbData($dbData), $result);
    }
}
