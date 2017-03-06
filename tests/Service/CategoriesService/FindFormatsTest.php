<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

class FindUsedFormatsTest extends AbstractCategoriesServiceTest
{
    public function testFindFormats()
    {
        $genre1 = $this->mockEntity('Format');
        $genre1->method('getId')->willReturn('PT082');

        $genre2 = $this->mockEntity('Format');
        $genre2->method('getId')->willReturn('PT083');

        $dbData = [['pip_id' => 'PT082'], ['pip_id' => 'PT083']];

        $this->mockRepository->expects($this->once())
            ->method('findAllByTypeAndMaxDepth')
            ->with('format', 2)
            ->willReturn($dbData);

        $result = $this->service()->findFormats();
        $this->assertEquals($this->categoriesFromDbData($dbData), $result);
    }
}
