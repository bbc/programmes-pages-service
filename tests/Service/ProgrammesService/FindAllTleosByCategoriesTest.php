<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Service\ProgrammesService;

class FindAllTleosByCategoriesTest extends AbstractProgrammesServiceTest
{
    public function testCommunicationWithDatabase()
    {
        $category = $this->createConfiguredMock(Genre::class, [
            'getDbId' => 1,
            'getChildren' => [
                $this->createConfiguredMock(Genre::class, ['getDbId' => 2]),
                $this->createConfiguredMock(Genre::class, ['getDbId' => 3]),
            ],
        ]);

        $this->mockRepository->expects($this->once())
            ->method('findTleosByCategories')
            ->with(
                [1, 2, 3],
                false,
                false,
                ProgrammesService::DEFAULT_LIMIT
            );

        $this->service()->findAllTleosByCategory($category);
    }

    /**
     * @dataProvider dbTleosProvider
     */
    public function testTleosAreReceivedFromRepository(array $expectedPids, array $dbTleosProvided)
    {
        $this->mockRepository->method('findTleosByCategories')->willReturn($dbTleosProvided);

        $tleos = $this->service()->findAllTleosByCategory($this->createMock(Genre::class));

        $this->assertContainsOnlyInstancesOf(Programme::class, $tleos);
        $this->assertCount(count($expectedPids), $tleos);
        foreach ($expectedPids as $i => $expectedPid) {
            $this->assertEquals($expectedPid, $tleos[$i]->getPid());
        }
    }

    public function dbTleosProvider(): array
    {
        return [
            'CASE: results found' => [
                ['b010t19z', 'b00swyx1'],
                [['pid' => 'b010t19z'], ['pid' => 'b00swyx1']],
            ],
            'CASE: no results found give an empty array' => [
                [],
                [],
            ],
        ];
    }
}
