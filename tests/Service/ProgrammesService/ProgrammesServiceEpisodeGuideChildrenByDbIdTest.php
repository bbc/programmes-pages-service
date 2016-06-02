<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class ProgrammesServiceFindEpisodeGuideChildrenByDbIdTest extends AbstractProgrammesServiceTest
{
    public function testFindEpisodeGuideChildrenByDbIdDefaultPagination()
    {
        $dbId = 1;

        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findEpisodeGuideChildren')
            ->with($dbId, 50, 0)
            ->willReturn($dbData);

        $result = $this->service()->findEpisodeGuideChildrenByDbId($dbId);
        $this->assertEquals($this->programmesFromDbData($dbData), $result);
    }

    public function testFindEpisodeGuideChildrenByDbIdCustomPagination()
    {
        $dbId = 1;

        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findEpisodeGuideChildren')
            ->with($dbId, 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findEpisodeGuideChildrenByDbId($dbId, 5, 3);
        $this->assertEquals($this->programmesFromDbData($dbData), $result);
    }

    public function testCountEpisodeGuideChildrenByDbId()
    {
        $dbId = 1;

        $this->mockRepository->expects($this->once())
            ->method('countEpisodeGuideChildren')
            ->with($dbId)
            ->willReturn(10);

        $this->assertEquals(10, $this->service()->countEpisodeGuideChildrenByDbId($dbId));
    }

    public function testFindEpisodeGuideChildrenByDbIdWithNonExistantPid()
    {
        $dbId = 999;

        $this->mockRepository->expects($this->once())
            ->method('findEpisodeGuideChildren')
            ->with($dbId, 50, 0)
            ->willReturn([]);

        $result = $this->service()->findEpisodeGuideChildrenByDbId($dbId);
        $this->assertEquals([], $result);

    }

    public function testCountEpisodeGuideChildrenByDbIdWithNonExistantPid()
    {
        $dbId = 999;

        $this->mockRepository->expects($this->once())
            ->method('countEpisodeGuideChildren')
            ->with($dbId)
            ->willReturn(0);

        $this->assertEquals(0, $this->service()->countEpisodeGuideChildrenByDbId($dbId));
    }
}
