<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

class FindPopulatedChildGenresTest extends AbstractCategoriesServiceTest
{
    public function testFindPopulatedChildGenres()
    {
        $genre = $this->mockEntity('Genre');
        $genre->method('getDbId')->willReturn(0);
        $dbData = [['pip_id' => 'C0001']];

        $this->mockRepository->expects($this->once())
            ->method('findPopulatedChildCategoriesByNetworkMedium')
            ->with(0, 'genre', 'tv')
            ->willReturn($dbData);

        $result = $this->service()->findPopulatedChildGenres($genre, 'tv');
        $this->assertEquals($this->categoriesFromDbData($dbData), $result);
    }
}
