<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

class FindTleosByFirstLetterTest extends AbstractAtozTitlesServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testFindTleosByFirstLetterPagination($expectedLimit, $expectedOffset, $paginationParams)
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

    public function testFindTleosByFirstLetterWithEmptyResult()
    {
         $this->mockRepository->method('findTleosByFirstLetter')->willReturn([]);

        $result = $this->service()->findTleosByFirstLetter('t');

        $this->assertEquals([], $result);
    }
}
