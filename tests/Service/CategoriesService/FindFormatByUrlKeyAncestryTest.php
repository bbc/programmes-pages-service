<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

class FindFormatByUrlKeyAncestryTest extends AbstractCategoriesServiceTest
{
    public function testFindFormatByUrlKeyAncestry()
    {
        $urlKey = 'key1';
        $dbData = ['pip_id' => 'F0001'];

        $this->mockRepository->expects($this->once())
            ->method('findByUrlKeyAncestryAndType')
            ->with([$urlKey], 'format')
            ->willReturn($dbData);

        $result = $this->service()->findFormatByUrlKeyAncestry($urlKey);
        $this->assertEquals($this->categoryFromDbData($dbData), $result);
    }

    public function testFindFormatByUrlKeyAncestryEmptyData()
    {
        $urlKey = 'key1';

        $this->mockRepository->expects($this->once())
            ->method('findByUrlKeyAncestryAndType')
            ->with([$urlKey], 'format')
            ->willReturn(null);

        $result = $this->service()->findFormatByUrlKeyAncestry($urlKey);
        $this->assertNull($result);
    }
}
