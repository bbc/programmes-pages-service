<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;

class FindGenresTest extends AbstractCategoriesServiceTest
{
    public function testFindGenresUseRepositoryCorrectly()
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('findTopLevelGenresAndChildren');

        $this->service()->findGenres();
    }

    public function testFindGenresResults()
    {
        $this->mockRepository->method('findTopLevelGenresAndChildren')->willReturn([['pip_id' => 'C00082'], ['pip_id' => 'C00083']]);

        $genres = $this->service()->findGenres();

        $this->assertContainsOnly(Genre::class, $genres);
        $this->assertSame(['C00082', 'C00083'], $this->extractIds($genres));
    }
}
