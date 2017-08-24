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

        $this->service()->findGenreByUrlKeyAncestry($urlKeyAncestry);
    }

    public function testFindGenreByUrlKeyAncestryResults()
    {
        $this->mockRepository->method('findByUrlKeyAncestryAndType')->willReturn(['pip_id' => 'C0001']);

        $format = $this->service()->findGenreByUrlKeyAncestry(['key1', 'key2']);

        $this->assertInstanceOf(Genre::class, $format);
        $this->assertEquals('C0001', $format->getId());
    }

    public function testFindGenreByUrlKeyAncestryEmptyData()
    {
        $this->mockRepository->method('findByUrlKeyAncestryAndType')->willReturn(null);

        $genre = $this->service()->findGenreByUrlKeyAncestry(['key1', 'key2']);

        $this->assertNull($genre);
    }
}
