<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;

class CountAllTleosByCategoryTest extends AbstractProgrammesServiceTest
{
    public function testCommunicationParamsWithRepository()
    {
        $category = $this->createConfiguredMock(Genre::class, ['getDbAncestryIds' => [1]]);

        $this->mockRepository->expects($this->once())
             ->method('countTleosByCategory')
             ->with($category->getDbAncestryIds(), false);

        $this->service()->countAllTleosByCategory($category);
    }

    public function testServiceReceiveCountFromDb()
    {
        $this->mockRepository->method('countTleosByCategory')->willReturn(2);

        $countTleos = $this->service()->countAllTleosByCategory($this->createMock(Genre::class));

        $this->assertEquals(2, $countTleos);
    }
}
