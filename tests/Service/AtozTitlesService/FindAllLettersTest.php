<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;

class FindAllLettersTest extends AbstractAtozTitlesServiceTest
{
    public function testFindAllLetters()
    {
        $dbData = ['a', 'b', 'c'];

        $this->mockRepository->expects($this->once())
            ->method('findAllLetters')
            ->with(null)
            ->willReturn($dbData);

        $result = $this->service()->findAllLetters();
        $this->assertEquals(['a', 'b', 'c'], $result);
    }

    public function testFindAllLettersWithMedium()
    {
        $dbData = ['a', 'b', 'c'];

        $this->mockRepository->expects($this->once())
            ->method('findAllLetters')
            ->with(NetworkMediumEnum::TV)
            ->willReturn($dbData);

        $result = $this->service()->findAllLetters(NetworkMediumEnum::TV);
        $this->assertEquals(['a', 'b', 'c'], $result);
    }
}
