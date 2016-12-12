<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

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

        $result = $this->service()->findGenreByUrlKeyAncestry($urlKeyAncestry);
        $this->assertEquals($this->categoryFromDbData($dbData), $result);
    }

    public function testFindGenreByUrlKeyAncestryEmptyData()
    {
        $urlKeyAncestry = ['key1', 'key2'];

        $this->mockRepository->expects($this->once())
            ->method('findByUrlKeyAncestryAndType')
            ->with($urlKeyAncestry, 'genre')
            ->willReturn(null);

        $result = $this->service()->findGenreByUrlKeyAncestry($urlKeyAncestry);
        $this->assertNull($result);
    }
}
