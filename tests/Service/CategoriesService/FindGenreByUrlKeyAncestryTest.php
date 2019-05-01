<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;

class FindGenreByUrlKeyAncestryTest extends AbstractCategoriesServiceTest
{
    public function testFindGenreByUrlKeyAncestryUseRepositoryCorrectly()
    {
        $urlKeyAncestry = ['key1', 'key2'];

        $this->mockRepository
            ->expects($this->once())
            ->method('findByUrlKeyAncestryAndType')
            ->with($urlKeyAncestry, 'genre');

        $this->service()->findGenreByUrlKeyAncestryWithDescendants($urlKeyAncestry);
    }

    public function testFindGenreByUrlKeyAncestryResults()
    {
        $this->mockRepository->method('findByUrlKeyAncestryAndType')->willReturn(['id' => 1, 'pip_id' => 'C0001']);
        $this->mockRepository->method('findByIdWithAllDescendants')
            ->with(1, 'genre')
            ->willReturn(['id' => 1, 'children' => [], 'pip_id' => 'C0001']);

        $format = $this->service()->findGenreByUrlKeyAncestryWithDescendants(['key1', 'key2']);

        $this->assertInstanceOf(Genre::class, $format);
        $this->assertEquals('C0001', $format ? $format->getId() : null);
    }

    public function testFindGenreByUrlKeyAncestryEmptyData()
    {
        $this->mockRepository->method('findByUrlKeyAncestryAndType')->willReturn(null);

        $genre = $this->service()->findGenreByUrlKeyAncestryWithDescendants(['key1', 'key2']);

        $this->assertNull($genre);
    }
}
