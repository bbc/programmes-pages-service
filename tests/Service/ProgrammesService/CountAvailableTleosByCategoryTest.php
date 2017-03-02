<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

class CountAvailableTleosByCategoryTest extends AbstractProgrammesServiceTest
{
    public function testCountAvailableTleosByCategory()
    {
        $dbId = 1;
        $dbData = 2;

        $category = $this->mockEntity('Genre', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('countTleosByCategory')
            ->with($category->getDbAncestryIds(), true)
            ->willReturn($dbData);

        $result = $this->service()->countAvailableTleosByCategory($category);
        $this->assertEquals($dbData, $result);
    }
}
