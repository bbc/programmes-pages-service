<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class FindBroadcastYearsAndMonthsByProgrammeTest extends AbstractCollapsedBroadcastServiceTest
{
    public function testRepositoryReceivesParamsToFindBroadcasts()
    {
        $dbAncestry = [1, 2, 3];

        $stubProgramme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => $dbAncestry]);

        $this->mockRepository->expects($this->once())
            ->method('FindAllYearsAndMonthsByProgramme')
            ->with($dbAncestry, false);

        $this->service()->findBroadcastYearsAndMonthsByProgramme($stubProgramme);
    }

    public function testFindBroadcastYearsAndMonthsByProgrammeResultsOrderedByTime()
    {
        $stubProgramme = $this->createConfiguredMock(Programme::class, ['getDbAncestryIds' => [1, 2, 3]]);

        $this->mockRepository
            ->method('FindAllYearsAndMonthsByProgramme')
            ->willReturn([
                 ['year' => '2016', 'month' => '8'],
                 ['year' => '2016', 'month' => '6'],
                 ['year' => '2015', 'month' => '12'],
                 ['year' => '2015', 'month' => '11'],
                 ['year' => '2015', 'month' => '6'],
                 ['year' => '2015', 'month' => '5'],
                 ['year' => '2014', 'month' => '6'],
             ]);

        $expectedResult = [
            2016 => [8, 6],
            2015 => [12, 11, 6, 5],
            2014 => [6],
        ];

        $this->assertSame(
            $expectedResult,
            $this->service()->findBroadcastYearsAndMonthsByProgramme($stubProgramme)
        );
    }
}
