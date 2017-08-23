<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

use BBC\ProgrammesPagesService\Domain\Entity\AtozTitle;

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

    public function testFindTleosByFirstLetterCustomPagination()
    {
        $dbData = [['firstLetter' => 't']];

        $this->mockRepository
            ->method('findTleosByFirstLetter')
            ->willReturn($dbData);

        $atozTleos = $this->service()->findTleosByFirstLetter('t');

        $this->assertCount(1, $atozTleos);
        $this->assertContainsOnly(AtozTitle::class, $atozTleos);
        $this->assertEquals('t', $atozTleos[0]->getFirstletter());
    }

    public function testFindTleosByFirstLetterWithEmptyResult()
    {
         $this->mockRepository->method('findTleosByFirstLetter')->willReturn([]);

        $atozTleos = $this->service()->findTleosByFirstLetter('t');

        $this->assertEquals([], $atozTleos);
    }
}
