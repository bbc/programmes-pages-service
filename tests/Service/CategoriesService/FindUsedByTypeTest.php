<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use InvalidArgumentException;

class FindUsedByTypeTest extends AbstractCategoriesServiceTest
{
    public function testFindUsedByGenre()
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

        $result = $this->service()->findUsedByType('genres');
        $this->assertEquals($this->categoriesFromDbData($dbData), $result);
    }

    public function testFindUsedByFormat()
    {
        $genre1 = $this->mockEntity('Format');
        $genre1->method('getId')->willReturn('PT082');

        $genre2 = $this->mockEntity('Format');
        $genre2->method('getId')->willReturn('PT083');

        $dbData = [['pip_id' => 'PT082'], ['pip_id' => 'PT083']];

        $this->mockRepository->expects($this->once())
            ->method('findUsedByType')
            ->with('format')
            ->willReturn($dbData);

        $result = $this->service()->findUsedByType('formats');
        $this->assertEquals($this->categoriesFromDbData($dbData), $result);
    }

    /** @expectedException InvalidArgumentException */
    public function testFindUsedByInvalidCategory()
    {
        $result = $this->service()->findUsedByType('invalid');
    }
}
