<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

class FindAllLettersTest extends AbstractAtozTitlesServiceTest
{
    public function testFindAllLetters()
    {
        $expectedResult = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '@'];
        $result = $this->service()->findAllLetters();
        $this->assertEquals($expectedResult, $result);
    }
}
