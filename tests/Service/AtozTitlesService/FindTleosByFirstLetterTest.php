<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

use BBC\ProgrammesPagesService\Domain\Entity\AtozTitle;

class FindTleosByFirstLetterTest extends AbstractAtozTitlesServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testFindTleosByFirstLetterPagination(int $expectedLimit, int $expectedOffset, array $paginationParams)
    {
        $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', false, $expectedLimit, $expectedOffset);

        $this->service()->findTleosByFirstLetter('t', ...$paginationParams);
    }

    public function paginationProvider()
    {
        return [
            'default pagination' => [300, 0, []],
            'custom pagination' => [5, 10, [5, 3]],
        ];
    }

    /**
     * @dataProvider resultsProvider
     */
    public function testFindTleosByFirstLetterResults(array $expectations, array $stubResults)
    {
        $this->mockRepository->method('findTleosByFirstLetter')->willReturn($stubResults);

        $atozTitles = $this->service()->findTleosByFirstLetter('t');

        $this->assertContainsOnly(AtozTitle::class, $atozTitles);
        $this->assertEquals($expectations, $this->extractFirstLetter($atozTitles));
    }

    public function resultsProvider(): array
    {
        return [
            // [expectations], [db data]
            [['t'], [['firstLetter' => 't']]],
            [[], []],
        ];
    }
}
