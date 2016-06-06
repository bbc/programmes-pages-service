<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

class ProgrammesServiceFindSiblingByProgrammeTest extends AbstractProgrammesServiceTest
{
    public function testFindNextSiblingById()
    {
        $programme = $this->mockEntity('Programme');
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findImmediateSibling')
            ->with($programme, 'next')
            ->willReturn($dbData);

        $result = $this->service()->findNextSiblingByProgramme($programme);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindNextSiblingByIdEmptyData()
    {
        $programme = $this->mockEntity('Programme');

        $this->mockRepository->expects($this->once())
            ->method('findImmediateSibling')
            ->with($programme, 'next')
            ->willReturn(null);

        $result = $this->service()->findNextSiblingByProgramme($programme);
        $this->assertEquals(null, $result);
    }

    public function testFindPreviousSiblingById()
    {
        $programme = $this->mockEntity('Programme');
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findImmediateSibling')
            ->with($programme, 'previous')
            ->willReturn($dbData);

        $result = $this->service()->findPreviousSiblingByProgramme($programme);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindPreviousSiblingByIdEmptyData()
    {
        $programme = $this->mockEntity('Programme');

        $this->mockRepository->expects($this->once())
            ->method('findImmediateSibling')
            ->with($programme, 'previous')
            ->willReturn(null);

        $result = $this->service()->findPreviousSiblingByProgramme($programme);
        $this->assertEquals(null, $result);
    }
}
