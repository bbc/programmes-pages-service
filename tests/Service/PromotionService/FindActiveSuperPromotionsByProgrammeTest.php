<?php

namespace Tests\BBC\ProgrammesPagesService\Service\PromotionService;

use DateTimeImmutable;

class FindActiveSuperPromotionsByProgrammeTest extends AbstractPromotionServiceTest
{
    public function testServiceFindActiveSuperPromotions()
    {
        $mockProgramme = $this->getMockProgramme();
        $dateTime = new DateTimeImmutable('1990-01-01 13:30:50');

        $this->mockRepository
            ->method('findActiveSuperPromotionsByAncestry')
            ->with($mockProgramme->getDbAncestryIds(), $dateTime, 300, 0)
            ->willReturn($this->stubDbData());

        $mappedPromotions = $this->service()->findActiveSuperPromotionsByProgramme(
            $mockProgramme,
            $dateTime
        );

        $this->assertEquals(
            $this->getDomainModelsFromDbData($this->stubDbData()),
            $mappedPromotions
        );
    }
}
