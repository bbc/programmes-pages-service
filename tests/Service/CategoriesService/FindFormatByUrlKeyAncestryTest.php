<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use BBC\ProgrammesPagesService\Domain\Entity\Format;

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

        $format = $this->service()->findFormatByUrlKeyAncestry($urlKey);

        $this->assertInstanceOf(Format::class, $format);
        $this->assertEquals('F0001', $format->getId());
    }

    public function testFindFormatByUrlKeyAncestryEmptyData()
    {
        $urlKey = 'key1';

        $this->mockRepository->expects($this->once())
            ->method('findByUrlKeyAncestryAndType')
            ->with([$urlKey], 'format')
            ->willReturn(null);

        $format = $this->service()->findFormatByUrlKeyAncestry($urlKey);

        $this->assertNull($format);
    }
}
