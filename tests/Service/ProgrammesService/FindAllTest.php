<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class FindAllTest extends AbstractProgrammesServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testProtocolWithRepository(int $expectedLimit, int $expectedOffset, array $paramsPagination)
    {
        $this->mockRepository->expects($this->once())
            ->method('findAllWithParents')
            ->with($expectedLimit, $expectedOffset);

        $this->service()->findAll(...$paramsPagination);
    }

    public function paginationProvider(): array
    {
        return [
            // [expectedLimit, expectedOffset, [limit, page]]
            'default pagination' => [300, 0, []],
            'custom pagination' => [3, 12, [3, 5]],
        ];
    }

    /**
     * @dataProvider dbEntitiesProvider
     */
    public function testResults($expectedPids, $dbEntitiesProvided)
    {
        $this->mockRepository->method('findAllWithParents')->willReturn($dbEntitiesProvided);

        $programmesWithParents = $this->service()->findAll();

        $this->assertContainsOnlyInstancesOf(Programme::class, $programmesWithParents);
        $this->assertCount(count($dbEntitiesProvided), $programmesWithParents);
        foreach ($expectedPids as $i => $expectedPid) {
            $this->assertEquals($expectedPid, $programmesWithParents[$i]->getPid());
        }
    }

    public function dbEntitiesProvider(): array
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

    public function testCountAll()
    {
        $this->mockRepository->expects($this->once())
            ->method('countAll')->willReturn(10);

        $this->assertEquals(10, $this->service()->countAll());
    }
}
