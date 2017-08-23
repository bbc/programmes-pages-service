<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

use BBC\ProgrammesPagesService\Domain\Entity\AtozTitle;

class FindAvailableTleosByFirstLetterTest extends AbstractAtozTitlesServiceTest
{
    /**
     * @dataProvider providerPagination
     */
    public function testFindAvailableTleosByFirstLetterPagination($expectedLimit, $expectedOffset, $paginationParams)
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
    
    public function testFindAvailableTleosByFirstLetterReturnRightResults()
    {
        $this->mockRepository->method('findTleosByFirstLetter')->willReturn([['firstLetter' => 't']]);

        $stubAtozTitles = $this->service()->findAvailableTleosByFirstLetter('t');

        $this->assertCount(1, $stubAtozTitles);
        $this->assertContainsOnly(AtozTitle::class, $stubAtozTitles);
        $this->assertSame('t', $stubAtozTitles[0]->getFirstletter());
    }

    public function testFindAvailableTleosByFirstLetterWithEmptyResult()
    {
        $this->mockRepository->method('findTleosByFirstLetter')->willReturn([]);

        $atozTitles = $this->service()->findAvailableTleosByFirstLetter('t');

        $this->assertEquals([], $atozTitles);
    }
}
