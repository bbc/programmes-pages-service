<?php

namespace Tests\BBC\ProgrammesPagesService\Service\PromotionService;

use DateTimeImmutable;

class FindActivePromotionsByProgrammeTest extends AbstractPromotionServiceTest
{
    public function testServiceFindActivePromotions()
    {
        $mockProgramme = $this->getMockProgramme();
        $dateTime = new DateTimeImmutable('1990-01-01 13:30:50');

        $this->mockRepository
            ->method('findActivePromotionsByPid')
            ->with($mockProgramme->getPid(), $dateTime, 300, 0)
            ->willReturn($this->stubDbData());

        $mappedPromotions = $this->service()->findActivePromotionsByProgramme(
            $mockProgramme,
            $dateTime
        );

        $this->assertEquals(
            $this->getDomainModelsFromDbData($this->stubDbData()),
            $mappedPromotions
        );
    }
}
