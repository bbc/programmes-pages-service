<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;

class CountAvailableTleosByCategoryTest extends AbstractProgrammesServiceTest
{
    public function testProtocolWithRepository()
    {
        $category = $this->createConfiguredMock(Genre::class, [
            'getDbId' => 1,
            'getChildren' => [
                $this->createChildCategory(2),
                $this->createChildCategory(3),
            ],
        ]);

        $this->mockRepository->expects($this->once())
            ->method('countTleosByCategories')
            ->with([1, 2, 3], true);

        $this->service()->countAvailableTleosByCategory($category);
    }

    public function testCountIsReceivedFromRepository()
    {
        $this->mockRepository->method('countTleosByCategories')->willReturn(2);

        $countAvailableTleos = $this->service()->countAvailableTleosByCategory($this->createMock(Genre::class));

        $this->assertEquals(2, $countAvailableTleos);
    }

    private function createChildCategory($dbId)
    {
        return $this->createConfiguredMock(Genre::class, ['getDbId' => $dbId]);
    }
}
