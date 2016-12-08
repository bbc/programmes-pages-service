<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtoZTitlesService;

use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;

class FindTleosByFirstLetterTest extends AbstractAtoZTitlesServiceTest
{
    public function testFindTleosByFirstLetterDefaultPagination()
    {
        $dbData = [['title' => 'things']];

        $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', null, false, 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findTleosByFirstLetter('t', null);
        $this->assertEquals($this->atoZTitlesFromDbData($dbData), $result);
    }

    public function testFindTleosByFirstLetterCustomPagination()
    {
        $dbData = [['title' => 'things']];

        $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', null, false, 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findTleosByFirstLetter('t', null, 5, 3);
        $this->assertEquals($this->atoZTitlesFromDbData($dbData), $result);
    }

    public function testFindTleosByFirstLetterWithEmptyResult()
    {
         $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', null, false, 300, 0)
            ->willReturn([]);

        $result = $this->service()->findTleosByFirstLetter('t', null);
        $this->assertEquals([], $result);
    }


    public function testFindTleosByFirstLetterWithMedium()
    {
        $dbData = [['title' => 'things']];

        $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', 'tv', false, 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findTleosByFirstLetter('t', 'tv');
        $this->assertEquals($this->atoZTitlesFromDbData($dbData), $result);
    }

    public function testCountTleosByFirstLetter()
    {
        $this->mockRepository->expects($this->once())
            ->method('countTleosByFirstLetter')
            ->with('t', null)
            ->willReturn(10);

        $result = $this->service()->countTleosByFirstLetter('t');
        $this->assertEquals(10, $result);
    }

    public function testCountTleosByFirstLetterWithMedium()
    {
        $this->mockRepository->expects($this->once())
            ->method('countTleosByFirstLetter')
            ->with('t', 'tv', false)
            ->willReturn(10);

        $result = $this->service()->countTleosByFirstLetter('t', 'tv');
        $this->assertEquals(10, $result);
    }
}
