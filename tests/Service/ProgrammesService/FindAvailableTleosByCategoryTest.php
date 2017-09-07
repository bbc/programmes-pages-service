<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Service\ProgrammesService;

class FindAvailableTleosByCategoryTest extends AbstractProgrammesServiceTest
{
    public function testServiceCommunicationWithRepository()
    {
        $category = $this->createConfiguredMock(Genre::class, ['getDbAncestryIds' => [1]]);

        $this->mockRepository->expects($this->once())
        ->method('findTleosByCategory')
        ->with(
             $category->getDbAncestryIds(),
             true,
             ProgrammesService::DEFAULT_LIMIT
        );

        $this->service()->findAvailableTleosByCategory($category);
    }

    /**
     * @dataProvider dbTleosProvider
     */
    public function testServiceCanReceiveTleosFromRepositoryByCategory(array $expectedPids, array $dbTleosProvided)
    {
        $this->mockRepository->method('findTleosByCategory')->willReturn($dbTleosProvided);

        $tleos = $this->service()->findAvailableTleosByCategory($this->createMock(Genre::class));

        $this->assertContainsOnlyInstancesOf(Programme::class, $tleos);
        $this->assertCount(count($expectedPids), $tleos);
        foreach ($expectedPids as $i => $expectedPid) {
            $this->assertEquals($expectedPid, $tleos[$i]->getPid());
        }
    }

    public function dbTleosProvider(): array
    {
        return [
            'CASE: ' => [
                ['b010t19z', 'b00swyx1'],
                [['pid' => 'b010t19z'], ['pid' => 'b00swyx1']],
            ],
            'CASE: ' => [
                [],
                [],
            ],
        ];
    }
}
