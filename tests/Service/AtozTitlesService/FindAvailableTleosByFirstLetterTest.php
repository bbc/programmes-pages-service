<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;

class FindAvailableTleosByFirstLetterTest extends AbstractAtozTitlesServiceTest
{
    public function testFindAvailableTleosByFirstLetterDefaultPagination()
    {
        $dbData = [['title' => 'things']];

        $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', null, true, 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findAvailableTleosByFirstLetter('t', null);
        $this->assertEquals($this->atoZTitlesFromDbData($dbData), $result);
    }

    public function testFindAvailableTleosByFirstLetterCustomPagination()
    {
        $dbData = [['title' => 'things']];

        $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', null, true, 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findAvailableTleosByFirstLetter('t', null, 5, 3);
        $this->assertEquals($this->atoZTitlesFromDbData($dbData), $result);
    }

    public function testFindAvailableTleosByFirstLetterWithEmptyResult()
    {
         $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', null, true, 300, 0)
            ->willReturn([]);

        $result = $this->service()->findAvailableTleosByFirstLetter('t', null);
        $this->assertEquals([], $result);
    }


    public function testFindAvailableTleosByFirstLetterWithMedium()
    {
        $dbData = [['title' => 'things']];

        $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', 'tv', true, 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findAvailableTleosByFirstLetter('t', 'tv');
        $this->assertEquals($this->atoZTitlesFromDbData($dbData), $result);
    }

    public function testCountAvailableTleosByFirstLetter()
    {
        $this->mockRepository->expects($this->once())
            ->method('countTleosByFirstLetter')
            ->with('t', null)
            ->willReturn(10);

        $result = $this->service()->countAvailableTleosByFirstLetter('t');
        $this->assertEquals(10, $result);
    }

    public function testCountAvailableTleosByFirstLetterWithMedium()
    {
        $this->mockRepository->expects($this->once())
            ->method('countTleosByFirstLetter')
            ->with('t', 'tv', true)
            ->willReturn(10);

        $result = $this->service()->countAvailableTleosByFirstLetter('t', 'tv');
        $this->assertEquals(10, $result);
    }
}
