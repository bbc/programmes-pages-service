<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;

class FindPopulatedChildGenresTest extends AbstractCategoriesServiceTest
{
    public function testFindPopulatedChildGenres()
    {
        $genre = $this->createMock(Genre::class);
        $genre->method('getDbId')->willReturn(0);

        $dbData = [['pip_id' => 'C0001']];

        $this->mockRepository->expects($this->once())
            ->method('findPopulatedChildCategories')
            ->with(0, 'genre')
            ->willReturn($dbData);

        $genres = $this->service()->findPopulatedChildGenres($genre);

        $this->assertCount(1, $genres);
        $this->assertContainsOnly(Genre::class, $genres);
        $this->assertEquals('C0001', $genres[0]->getId());
    }
}
