<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;

class CountAllTleosByCategoryTest extends AbstractProgrammesServiceTest
{
    public function testCommunicationParamsWithRepository()
    {
        $category = $this->createConfiguredMock(
            Genre::class,
            [
                'getDbId' => 1,
                'getChildren' => [
                    $this->createChildCategory(2),
                    $this->createChildCategory(3),
                ],
            ]
        );

        $this->mockRepository->expects($this->once())
             ->method('countTleosByCategories')
             ->with([1, 2, 3], false);

        $this->service()->countAllTleosByCategory($category);
    }

    public function testServiceReceiveCountFromDb()
    {
        $this->mockRepository->method('countTleosByCategories')->willReturn(2);

        $countTleos = $this->service()->countAllTleosByCategory($this->createMock(Genre::class));

        $this->assertEquals(2, $countTleos);
    }

    private function createChildCategory($dbId)
    {
        return $this->createConfiguredMock(Genre::class, ['getDbId' => $dbId]);
    }
}
