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

    public function providerPagination()
    {
        return [
            'default pagination' => [300, 0, []],
            'custom pagination' => [5, 10, [5, 3]],
        ];
    }

    public function testFindAvailableTleosByFirstLetterReturnRightResults()
    {
        $this->mockRepository->method('findTleosByFirstLetter')->willReturn([['title' => 'things']]);

        $atozTitles = $this->service()->findAvailableTleosByFirstLetter('t');

        $this->assertCount(1, $atozTitles);
        $this->assertContainsOnly(AtozTitle::class, $atozTitles);
        $this->assertEquals('things', $atozTitles[0]->getTitle());
    }

    public function testFindAvailableTleosByFirstLetterWithEmptyResult()
    {
        $this->mockRepository->method('findTleosByFirstLetter')->willReturn([]);

        $atozTitles = $this->service()->findAvailableTleosByFirstLetter('t');

        $this->assertEquals([], $atozTitles);
    }
}
