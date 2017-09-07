<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class KeywordsTest extends AbstractProgrammesServiceTest
{
    public function testfindByKeywords()
    {
        $dbData = [['pid' => 'b010t19z'], ['pid' => 'b00swyx1']];

        $keywords = 'Wibble bark otter';

        $this->mockRepository->expects($this->once())
            ->method('findByKeywords')
            ->with($keywords, false, 300, 0)
            ->willReturn($dbData);

        $programmes = $this->service()->searchByKeywords($keywords);

        $this->assertContainsOnlyInstancesOf(Programme::class, $programmes);
    }

    public function testCountByKeywords()
    {
        $keywords = 'KHAAAAAAAAAAN';

        $this->mockRepository->expects($this->once())
            ->method('countByKeywords')
            ->with($keywords, false)
            ->willReturn(10);

        $this->assertEquals(10, $this->service()->countByKeywords($keywords));
    }
}
