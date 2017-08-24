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

    public function testFindPopulatedChildGenres()
    {
        $this->mockRepository->method('findPopulatedChildCategories')->willReturn([['pip_id' => 'C0001']]);

        $dummyGenre = $this->createMock(Genre::class);
        $stubGenres = $this->service()->findPopulatedChildGenres($dummyGenre);

        $this->assertContainsOnly(Genre::class, $stubGenres);
        $this->assertSame(['C0001'], $this->extractIds($stubGenres));
    }
}
