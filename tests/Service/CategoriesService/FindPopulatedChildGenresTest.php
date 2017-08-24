<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;

class FindPopulatedChildGenresTest extends AbstractCategoriesServiceTest
{
    public function testFindPopulatedChildGenresUseRepositoryCorrectly()
    {
        $stubGenre = $this->createMock(Genre::class);
        $stubGenre->method('getDbId')->willReturn(999);

        $this->mockRepository->expects($this->once())
            ->method('findPopulatedChildCategories')
            ->with($stubGenre->getDbId(), 'genre');

        $this->service()->findPopulatedChildGenres($stubGenre);
    }

    /**
     * @dataProvider resultsProvider
     */
    public function testFindPopulatedChildGenresOneResult(array $expectedIds, array $dbResults)
    {
        $this->mockRepository->method('findPopulatedChildCategories')->willReturn($dbResults);

        $dummyGenre = $this->createMock(Genre::class);
        $stubGenres = $this->service()->findPopulatedChildGenres($dummyGenre);

        $this->assertContainsOnly(Genre::class, $stubGenres);
        $this->assertEquals($expectedIds, $this->extractIds($stubGenres));
    }

    public function resultsProvider(): array
    {
        return[
            [['C0001', 'C0002'],[['pip_id' => 'C0001'], ['pip_id' => 'C0002']]],
            [[], []],
        ];
    }
}
