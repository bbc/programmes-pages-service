<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeContainer;

class FindChildrenSeriesByParentTest extends AbstractProgrammesServiceTest
{
    public function testRepositoryReceiveAllParamsToFetchData()
    {
        $programmeContainerParent = $this->createConfiguredMock(ProgrammeContainer::class, ['getDbId' => 0]);

        $this->mockRepository->expects($this->once())
            ->method('findChildrenSeriesByParent')
            ->with($programmeContainerParent->getDbId());

        $this->service()->findChildrenSeriesByParent($programmeContainerParent);
    }

    /**
     * @dataProvider dbSeriesProvider
     */
    public function testArrayIsReturnedWithSeriesOnIt(array $expectedPids, array $dbSeriesProvided)
    {
        $programmeContainerParent = $this->createConfiguredMock(ProgrammeContainer::class, ['getDbId' => 0]);

        $this->mockRepository
            ->method('findChildrenSeriesByParent')
            ->willReturn($dbSeriesProvided);

        $series = $this->service()->findChildrenSeriesByParent($programmeContainerParent);

        // we should test that is a serie, but that funcionality is really tested by the mapper. In here we test
        // the most generic case
        $this->assertContainsOnlyInstancesOf(Programme::class, $series);
        $this->assertCount(count($dbSeriesProvided), $series);
        foreach ($expectedPids as $i => $expectedPid) {
            $this->assertEquals($expectedPid, $series[$i]->getPid());
        }
    }

    public function dbSeriesProvider(): array
    {
        return [
            'CASE: series found in db' => [
                ['b010t19z', 'b00swyx1'],
                [['pid' => 'b010t19z'], ['pid' => 'b00swyx1']],
            ],
            'CASE: no series found' => [
                [],
                [],
            ],
        ];
    }
}
