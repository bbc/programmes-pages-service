<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use BBC\ProgrammesPagesService\Domain\Entity\Format;

class FindFormatsTest extends AbstractCategoriesServiceTest
{
    public function testFindFormats()
    {
        $this->mockRepository->expects($this->once())
            ->method('findAllByTypeAndMaxDepth')
            ->with('format', 2)
            ->willReturn([['pip_id' => 'PT082'], ['pip_id' => 'PT083']]);

        $stubFormats = $this->service()->findFormats();

        $this->assertCount(2, $stubFormats);
        $this->assertContainsOnly(Format::class, $stubFormats);
        $this->assertEquals('PT082', $stubFormats[0]->getId());
        $this->assertEquals('PT083', $stubFormats[1]->getId());
    }
}
