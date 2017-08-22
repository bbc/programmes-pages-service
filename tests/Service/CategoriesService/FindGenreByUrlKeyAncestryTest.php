<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;

class FindGenreByUrlKeyAncestryTest extends AbstractCategoriesServiceTest
{
    public function testFindGenreByUrlKeyAncestry()
    {
        $urlKeyAncestry = ['key1', 'key2'];
        $dbData = ['pip_id' => 'C0001'];

        $this->mockRepository->expects($this->once())
            ->method('findByUrlKeyAncestryAndType')
            ->with($urlKeyAncestry, 'genre')
            ->willReturn($dbData);

        $format = $this->service()->findGenreByUrlKeyAncestry($urlKeyAncestry);

        $this->assertInstanceOf(Genre::class, $format);
        $this->assertEquals('C0001', $format->getId());
    }

    public function testFindGenreByUrlKeyAncestryEmptyData()
    {
        $urlKeyAncestry = ['key1', 'key2'];

        $this->mockRepository->expects($this->once())
            ->method('findByUrlKeyAncestryAndType')
            ->with($urlKeyAncestry, 'genre')
            ->willReturn(null);

        $genre = $this->service()->findGenreByUrlKeyAncestry($urlKeyAncestry);
        $this->assertNull($genre);
    }
}
