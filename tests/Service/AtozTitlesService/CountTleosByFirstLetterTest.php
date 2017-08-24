<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

class CountTleosByFirstLetterTest extends AbstractAtozTitlesServiceTest
{
    public function testCountTleosByFirstLetter()
    {
        $this->mockRepository->expects($this->once())
            ->method('countTleosByFirstLetter')->with('t')->willReturn(10);

        $this->assertEquals(10, $this->service()->countTleosByFirstLetter('t'));
    }
}
