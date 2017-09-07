<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class FindEpisodeGuideChildrenTest extends AbstractProgrammesServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testProtocolWithRepository(int $expectedLimit, int $expectedOffset, array $paginationParams)
    {
        $programme = $this->createConfiguredMock(Programme::class, ['getDbId' => 1]);

        $this->mockRepository->expects($this->once())
            ->method('findEpisodeGuideChildren')
            ->with($programme->getDbId(), $expectedLimit, $expectedOffset);

        $this->service()->findEpisodeGuideChildren($programme, ...$paginationParams);
    }

    public function paginationProvider(): array
    {
        return [
            // [expectedLimit, expectedOffset, [limit, page]]
            'default pagination' => [300, 0, []],
            'custom pagination' => [3, 12, [3, 5]],
        ];
    }

    public function testFindEpisodeGuideChildrenWithNonExistantPid()
    {
        $dbId = 999;
        $programme = $this->mockEntity('Programme', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findEpisodeGuideChildren')
            ->with($dbId, 5, 10)
            ->willReturn([]);

        $result = $this->service()->findEpisodeGuideChildren($programme, 5, 3);
        $this->assertEquals([], $result);
    }

    public function testCountReturnAnIntegerWithAmountOfEpisodes()
    {
        $programme = $this->createConfiguredMock(Programme::class, ['getDbId' => 1]);

        $this->mockRepository
            ->method('countEpisodeGuideChildren')
            ->with($programme->getDbId())
            ->will($this->onConsecutiveCalls(0, 1, 10));

        $this->assertEquals(0, $this->service()->countEpisodeGuideChildren($programme));
        $this->assertEquals(1, $this->service()->countEpisodeGuideChildren($programme));
        $this->assertEquals(10, $this->service()->countEpisodeGuideChildren($programme));
    }
}
