<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

class CountAllTleosByCategoryTest extends AbstractProgrammesServiceTest
{
    public function testCountAllTleosByCategory()
    {
        $dbId = 1;
        $dbData = 2;

        $category = $this->mockEntity('Genre', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('countTleosByCategory')
            ->with($category->getDbAncestryIds(), false)
            ->willReturn($dbData);

        $result = $this->service()->countAllTleosByCategory($category);
        $this->assertEquals($dbData, $result);
    }
}
