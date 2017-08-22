<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;

class FindGenresTest extends AbstractCategoriesServiceTest
{
    public function testFindGenres()
    {
        $dbData = [['pip_id' => 'C00082'], ['pip_id' => 'C00083']];

        $this->mockRepository->expects($this->once())
            ->method('findAllByTypeAndMaxDepth')
            ->with('genre', 2)
            ->willReturn($dbData);

        $genres = $this->service()->findGenres();

        $this->assertCount(2, $genres);
        $this->assertContainsOnly(Genre::class, $genres);
        $this->assertEquals('C00082', $genres[0]->getId());
        $this->assertEquals('C00083', $genres[1]->getId());

    }
}
