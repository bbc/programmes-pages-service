<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

class FindGenresTest extends AbstractCategoriesServiceTest
{
    public function testFindGenres()
    {
        $genre1 = $this->mockEntity('Genre');
        $genre1->method('getId')->willReturn('C00082');

        $genre2 = $this->mockEntity('Genre');
        $genre2->method('getId')->willReturn('C00083');

        $dbData = [['pip_id' => 'C00082'], ['pip_id' => 'C00083']];

        $this->mockRepository->expects($this->once())
            ->method('findAllByTypeAndMaxDepth')
            ->with('genre', 2)
            ->willReturn($dbData);

        $result = $this->service()->findGenres();
        $this->assertEquals($this->categoriesFromDbData($dbData), $result);
    }
}
