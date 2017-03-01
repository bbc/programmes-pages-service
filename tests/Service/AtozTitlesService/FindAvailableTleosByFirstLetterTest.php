<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

class FindAvailableTleosByFirstLetterTest extends AbstractAtozTitlesServiceTest
{
    public function testFindAvailableTleosByFirstLetterDefaultPagination()
    {
        $dbData = [['title' => 'things']];

        $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', true, 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findAvailableTleosByFirstLetter('t');
        $this->assertEquals($this->atoZTitlesFromDbData($dbData), $result);
    }

    public function testFindAvailableTleosByFirstLetterCustomPagination()
    {
        $dbData = [['title' => 'things']];

        $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', true, 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findAvailableTleosByFirstLetter('t', 5, 3);
        $this->assertEquals($this->atoZTitlesFromDbData($dbData), $result);
    }

    public function testFindAvailableTleosByFirstLetterWithEmptyResult()
    {
         $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', true, 300, 0)
            ->willReturn([]);

        $result = $this->service()->findAvailableTleosByFirstLetter('t');
        $this->assertEquals([], $result);
    }


    public function testCountAvailableTleosByFirstLetter()
    {
        $this->mockRepository->expects($this->once())
            ->method('countTleosByFirstLetter')
            ->with('t')
            ->willReturn(10);

        $result = $this->service()->countAvailableTleosByFirstLetter('t');
        $this->assertEquals(10, $result);
    }
}
