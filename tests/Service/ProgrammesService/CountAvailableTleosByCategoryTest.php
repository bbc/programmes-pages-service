<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;

class CountAvailableTleosByCategoryTest extends AbstractProgrammesServiceTest
{
    public function testProtocolWithRepository()
    {
        $category = $this->createConfiguredMock(Genre::class, ['getDbAncestryIds' => [1]]);

        $this->mockRepository->expects($this->once())
            ->method('countTleosByCategory')
            ->with($category->getDbAncestryIds(), true);

        $this->service()->countAvailableTleosByCategory($category);
    }

    public function testCountIsReceivedFromRepository()
    {
        $this->mockRepository->method('countTleosByCategory')->willReturn(2);

        $countAvailableTleos = $this->service()->countAvailableTleosByCategory($this->createMock(Genre::class));

        $this->assertEquals(2, $countAvailableTleos);
    }
}
