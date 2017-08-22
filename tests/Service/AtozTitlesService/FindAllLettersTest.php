<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

use BBC\ProgrammesPagesService\Service\AtozTitlesService;

class FindAllLettersTest extends AbstractAtozTitlesServiceTest
{
    public function testFindAllLetters()
    {
        $this->assertEquals(
            AtozTitlesService::LETTERS,
            $this->service()->findAllLetters()
        );
    }
}
