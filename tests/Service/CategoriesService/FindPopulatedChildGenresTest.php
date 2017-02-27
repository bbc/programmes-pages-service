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
            ->method('findPopulatedChildCategories')
            ->with(0, 'genre')
            ->willReturn($dbData);

        $result = $this->service()->findPopulatedChildGenres($genre);
        $this->assertEquals($this->categoriesFromDbData($dbData), $result);
    }
}
