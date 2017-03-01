<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

class FindTleosByFirstLetterTest extends AbstractAtozTitlesServiceTest
{
    public function testFindTleosByFirstLetterDefaultPagination()
    {
        $dbData = [['title' => 'things']];

        $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', false, 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findTleosByFirstLetter('t');
        $this->assertEquals($this->atoZTitlesFromDbData($dbData), $result);
    }

    public function testFindTleosByFirstLetterCustomPagination()
    {
        $dbData = [['title' => 'things']];

        $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', false, 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findTleosByFirstLetter('t', 5, 3);
        $this->assertEquals($this->atoZTitlesFromDbData($dbData), $result);
    }

    public function testFindTleosByFirstLetterWithEmptyResult()
    {
         $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', false, 300, 0)
            ->willReturn([]);

        $result = $this->service()->findTleosByFirstLetter('t');
        $this->assertEquals([], $result);
    }

    public function testCountTleosByFirstLetter()
    {
        $this->mockRepository->expects($this->once())
            ->method('countTleosByFirstLetter')
            ->with('t')
            ->willReturn(10);

        $result = $this->service()->countTleosByFirstLetter('t');
        $this->assertEquals(10, $result);
    }
}
