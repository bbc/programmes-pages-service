<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;

class FindPopulatedChildGenresTest extends AbstractCategoriesServiceTest
{
    public function testFindPopulatedChildGenres()
    {
        $genreDbId = 999;

        $this->mockRepository->expects($this->once())
            ->method('findPopulatedChildCategories')
            ->with($genreDbId, 'genre')
            ->willReturn([['pip_id' => 'C0001']]);

        $stubGenre = $this->createMock(Genre::class);
        $stubGenre->method('getDbId')->willReturn($genreDbId);

        $stubGenres = $this->service()->findPopulatedChildGenres($stubGenre);

        $this->assertCount(1, $stubGenres);
        $this->assertContainsOnly(Genre::class, $stubGenres);
        $this->assertEquals('C0001', $stubGenres[0]->getId());
    }
}
