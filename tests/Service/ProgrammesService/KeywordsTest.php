<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

class KeywordsTest extends AbstractProgrammesServiceTest
{
    public function testFindProgrammesByKeywords()
    {
        $dbData = [['pid' => 'b010t19z'], ['pid' => 'b00swyx1']];

        $keywords = 'Wibble bark otter';

        $this->mockRepository->expects($this->once())
            ->method('findByKeywords')
            ->with($keywords, 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findProgrammesByKeywords($keywords);
        $this->assertEquals($this->programmesFromDbData($dbData), $result);
    }

    public function testCountProgrammesByKeywords()
    {
        $keywords = 'KHAAAAAAAAAAN';

        $this->mockRepository->expects($this->once())
            ->method('countByKeywords')
            ->with($keywords)
            ->willReturn(10);

        $this->assertEquals(10, $this->service()->countProgrammesByKeywords($keywords));
    }
}
