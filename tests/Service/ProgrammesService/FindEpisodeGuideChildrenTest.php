<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

class FindEpisodeGuideChildrenTest extends AbstractProgrammesServiceTest
{
    public function testFindEpisodeGuideChildrenDefaultPagination()
    {
        $dbId = 1;
        $programme = $this->mockEntity('Programme', $dbId);
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findEpisodeGuideChildren')
            ->with($dbId, 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findEpisodeGuideChildren($programme);
        $this->assertEquals($this->programmesFromDbData($dbData), $result);
    }

    public function testFindEpisodeGuideChildrenCustomPagination()
    {
        $dbId = 1;
        $programme = $this->mockEntity('Programme', $dbId);
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findEpisodeGuideChildren')
            ->with($dbId, 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findEpisodeGuideChildren($programme, 5, 3);
        $this->assertEquals($this->programmesFromDbData($dbData), $result);
    }

    public function testCountEpisodeGuideChildren()
    {
        $dbId = 1;
        $programme = $this->mockEntity('Programme', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('countEpisodeGuideChildren')
            ->with($dbId)
            ->willReturn(10);

        $this->assertEquals(10, $this->service()->countEpisodeGuideChildren($programme));
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

    public function testCountEpisodeGuideChildrenWithNonExistantPid()
    {
        $dbId = 999;
        $programme = $this->mockEntity('Programme', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('countEpisodeGuideChildren')
            ->with($dbId)
            ->willReturn(0);

        $this->assertEquals(0, $this->service()->countEpisodeGuideChildren($programme));
    }
}
