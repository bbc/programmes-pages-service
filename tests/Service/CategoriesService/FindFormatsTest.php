<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use BBC\ProgrammesPagesService\Domain\Entity\Format;

class FindFormatsTest extends AbstractCategoriesServiceTest
{
    public function testFindFormats()
    {
        $dbData = [['pip_id' => 'PT082'], ['pip_id' => 'PT083']];

        $this->mockRepository->expects($this->once())
            ->method('findAllByTypeAndMaxDepth')
            ->with('format', 2)
            ->willReturn($dbData);

        $formats = $this->service()->findFormats();

        $this->assertCount(2, $formats);
        $this->assertContainsOnly(Format::class, $formats);
        $this->assertEquals('PT082', $formats[0]->getId());
        $this->assertEquals('PT083', $formats[1]->getId());
    }
}
