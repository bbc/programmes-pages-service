<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use BBC\ProgrammesPagesService\Domain\Entity\Format;

class FindFormatsTest extends AbstractCategoriesServiceTest
{
    public function testFindFormatsUseRepositoryCorrectly()
    {
        $this->mockRepository->expects($this->once())
            ->method('findAllByTypeAndMaxDepth')
            ->with('format', 2);

        $this->service()->findFormats();
    }

    public function testFindFormats()
    {
        $this->mockRepository->method('findAllByTypeAndMaxDepth')->willReturn([['pip_id' => 'PT082'], ['pip_id' => 'PT083']]);

        $formats = $this->service()->findFormats();

        $this->assertContainsOnly(Format::class, $formats);
        $this->assertSame(['PT082', 'PT083'], $this->extractIds($formats));
    }
}
