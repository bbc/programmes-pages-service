<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use BBC\ProgrammesPagesService\Domain\Entity\Format;

class FindFormatByUrlKeyAncestryTest extends AbstractCategoriesServiceTest
{
    public function testFindFormatByUrlKeyAncestryUseRepositoryCorrectly()
    {
        $urlKey = 'key1';

        $this->mockRepository
            ->expects($this->once())
            ->method('findByUrlKeyAncestryAndType')
            ->with([$urlKey], 'format');

        $this->service()->findFormatByUrlKeyAncestry($urlKey);
    }

    public function testFindFormatByUrlKeyAncestryResults()
    {
        $this->mockRepository->method('findByUrlKeyAncestryAndType')->willReturn(['pip_id' => 'F0001']);

        $format = $this->service()->findFormatByUrlKeyAncestry('key1');

        $this->assertInstanceOf(Format::class, $format);
        $this->assertEquals('F0001', $format ? $format->getId() : null);
    }

    public function testFindFormatByUrlKeyAncestryNoResults()
    {
        $this->mockRepository->method('findByUrlKeyAncestryAndType')->willReturn(null);

        $format = $this->service()->findFormatByUrlKeyAncestry('key1');

        $this->assertNull($format);
    }
}
