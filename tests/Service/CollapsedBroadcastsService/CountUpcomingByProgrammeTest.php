<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use DateTimeImmutable;

class CountUpcomingByProgrammeTest extends AbstractCollapsedBroadcastServiceTest
{
    public function testProperDataToFetchDataFromDb()
    {
        $stubProgramme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => [1, 2, 3]]);

        $this->mockRepository->expects($this->once())
            ->method('countUpcomingByProgramme')
            ->with($stubProgramme->getDbAncestryIds(), false, $this->lessThanOrEqual(new DateTimeImmutable()));

        $this->service()->countUpcomingByProgramme($stubProgramme);
    }

    public function testServiceReturnCountFromDatabase()
    {
        $this->mockRepository->method('countUpcomingByProgramme')->willReturn(12545);

        $this->assertEquals(
            12545,
            $this->service()->countUpcomingByProgramme($this->createMock(Programme::class))
        );
    }
}
