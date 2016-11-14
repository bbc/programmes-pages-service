<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

class FindUsedGenresTest extends AbstractCategoriesServiceTest
{
    public function testFindUsedGenres()
    {
        $genre1 = $this->mockEntity('Genre');
        $genre1->method('getId')->willReturn('C00082');

        $genre2 = $this->mockEntity('Genre');
        $genre2->method('getId')->willReturn('C00083');

        $dbData = [['pip_id' => 'C00082'], ['pip_id' => 'C00083']];

        $this->mockRepository->expects($this->once())
            ->method('findUsedByType')
            ->with('genre')
            ->willReturn($dbData);

        $result = $this->service()->findUsedGenres();
        $this->assertEquals($this->categoriesFromDbData($dbData), $result);
    }
}
