<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Service\ProgrammesService;

class FindAllByCategoryTest extends AbstractProgrammesServiceTest
{
    public function testFindAllByCategory()
    {
        $medium = 'tv';
        $dbId = 1;
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        //$category = new Genre([1,2,3], 3, 'mytitle', 'myurlkey');
        $category = $this->mockEntity('Genre', $dbId);

        $this->mockRepository->expects($this->once())
                             ->method('findTleosByCategory')
                             ->with(
                                 $category->getDbAncestryIds(),
                                 false,
                                 $medium,
                                 ProgrammesService::DEFAULT_LIMIT
                             )
                             ->willReturn($dbData);

        $result = $this->service()->findAllByCategory($category, $medium);
        $this->assertEquals($this->programmesFromDbData($dbData), $result);
    }
}
