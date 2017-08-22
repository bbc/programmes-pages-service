<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;


class CountAvailableTleosByFirstLetterTest extends AbstractAtozTitlesServiceTest
{
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
