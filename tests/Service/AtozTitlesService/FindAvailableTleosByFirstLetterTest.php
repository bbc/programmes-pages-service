<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

use BBC\ProgrammesPagesService\Domain\Entity\AtozTitle;

class FindAvailableTleosByFirstLetterTest extends AbstractAtozTitlesServiceTest
{
    /**
     * @dataProvider providerPagination
     */
    public function testFindAvailableTleosByFirstLetterPagination(int $expectedLimit, int $expectedOffset, array $paginationParams)
    {
        $this->mockRepository->expects($this->once())
            ->method('findTleosByFirstLetter')
            ->with('t', true, $expectedLimit, $expectedOffset);

        $this->service()->findAvailableTleosByFirstLetter('t', ...$paginationParams);
    }

    public function providerPagination(): array
    {
        return [
            // expectedLimit, expectedOffset, [limit, page]
            'default pagination' => [300, 0, []],
            'custom pagination' => [5, 10, [5, 3]],
        ];
    }

    /**
     * @dataProvider resultsProvider
     */
    public function testFindAvailableTleosByFirstLetterReturnRightResults(array $expectations, array $dbResults)
    {
        $this->mockRepository->method('findTleosByFirstLetter')->willReturn($dbResults);

        $atozTitles = $this->service()->findAvailableTleosByFirstLetter('t');

        $this->assertContainsOnly(AtozTitle::class, $atozTitles);
        $this->assertSame($expectations, $this->extractFirstLetter($atozTitles));
    }

    public function resultsProvider(): array
    {
        return [
            // [expectations], [db data]
            [['t'], [['firstLetter' => 't']]],
            [[],[]],
        ];
    }
}
