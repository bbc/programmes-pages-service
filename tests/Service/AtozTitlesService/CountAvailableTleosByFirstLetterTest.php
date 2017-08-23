<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

class CountAvailableTleosByFirstLetterTest extends AbstractAtozTitlesServiceTest
{
    public function testCountAvailableTleosByFirstLetter()
    {
        $this->mockRepository->expects($this->once())
            ->method('countTleosByFirstLetter')->with('t')->willReturn(10);

        $this->assertEquals(10, $this->service()->countAvailableTleosByFirstLetter('t'));
    }
}
