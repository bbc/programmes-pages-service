<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

use BBC\ProgrammesPagesService\Service\AtozTitlesService;

class FindAllLettersTest extends AbstractAtozTitlesServiceTest
{
    public function testFindAllLetters()
    {
        $result = $this->service()->findAllLetters();
        $this->assertEquals(AtozTitlesService::LETTERS, $result);
    }
}
