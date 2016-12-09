<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

class KeywordsTest extends AbstractProgrammesServiceTest
{
    public function testfindByKeywords()
    {
        $dbData = [['pid' => 'b010t19z'], ['pid' => 'b00swyx1']];

        $keywords = 'Wibble bark otter';

        $this->mockRepository->expects($this->once())
            ->method('findByKeywords')
            ->with($keywords, null, false, 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->searchByKeywords($keywords);
        $this->assertEquals($this->programmesFromDbData($dbData), $result);
    }

    public function testCountByKeywords()
    {
        $keywords = 'KHAAAAAAAAAAN';

        $this->mockRepository->expects($this->once())
            ->method('countByKeywords')
            ->with($keywords, null, false)
            ->willReturn(10);

        $this->assertEquals(10, $this->service()->countByKeywords($keywords));
    }
}
