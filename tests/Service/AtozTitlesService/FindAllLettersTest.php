<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

class FindAllLettersTest extends AbstractAtozTitlesServiceTest
{
    public function testFindAllLetters()
    {
        $dbData = ['a', 'b', 'c'];

        $this->mockRepository->expects($this->once())
            ->method('findAllLetters')
            ->with()
            ->willReturn($dbData);

        $result = $this->service()->findAllLetters();
        $this->assertEquals(['a', 'b', 'c'], $result);
    }
}
