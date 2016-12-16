<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Service\ProgrammesService;

class FindAvailableByCategoryTest extends AbstractProgrammesServiceTest
{
    public function testFindAvailableTleosByCategory()
    {
        $medium = 'tv';
        $dbId = 1;
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];
        $category = $this->mockEntity('Genre', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findTleosByCategory')
            ->with(
             $category->getDbAncestryIds(),
             true,
             $medium,
             ProgrammesService::DEFAULT_LIMIT
            )
            ->willReturn($dbData);

        $result = $this->service()->findAvailableTleosByCategory($category, $medium);
        $this->assertEquals($this->programmesFromDbData($dbData), $result);
    }
}
