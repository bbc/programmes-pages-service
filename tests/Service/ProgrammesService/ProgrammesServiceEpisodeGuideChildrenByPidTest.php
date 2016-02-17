<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\EntityCollectionServiceResult;

class ProgrammesServiceFindEpisodeGuideChildrenByPidTest extends AbstractProgrammesServiceTest
{
    public function testFindEpisodeGuideChildrenByPidDefaultPagination()
    {
        $pid = new Pid('b010t19z');
        $mockBrandId = 1;

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($this->equalTo($pid))
            ->willReturn(['id' => $mockBrandId]);

        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findEpisodeGuideChildren')
            ->with($this->equalTo($mockBrandId), $this->equalTo(50), $this->equalTo(0))
            ->willReturn($dbData);

        $expectedResult = new EntityCollectionServiceResult(
            $this->programmesFromDbData($dbData),
            50,
            1
        );

        $result = $this->programmesService()->findEpisodeGuideChildrenByPid($pid);
        $this->assertEquals($expectedResult, $result);
    }

    public function testFindEpisodeGuideChildrenByPidCustomPagination()
    {
        $pid = new Pid('b010t19z');
        $mockBrandId = 1;

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($this->equalTo($pid))
            ->willReturn(['id' => $mockBrandId]);

        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findEpisodeGuideChildren')
            ->with($this->equalTo($mockBrandId), $this->equalTo(5), $this->equalTo(10))
            ->willReturn($dbData);

        $expectedResult = new EntityCollectionServiceResult(
            $this->programmesFromDbData($dbData),
            5,
            3
        );

        $result = $this->programmesService()->findEpisodeGuideChildrenByPid($pid, 5, 3);
        $this->assertEquals($expectedResult, $result);
    }

    public function testCountEpisodeGuideChildrenByPid()
    {
        $pid = new Pid('b010t19z');
        $mockBrandId = 1;

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($this->equalTo($pid))
            ->willReturn(['id' => $mockBrandId]);

        $this->mockRepository->expects($this->once())
            ->method('countEpisodeGuideChildren')
            ->with($this->equalTo($mockBrandId))
            ->willReturn(10);

        $this->assertEquals(10, $this->programmesService()->countEpisodeGuideChildrenByPid($pid));
    }

    public function testFindEpisodeGuideChildrenByPidWithNonExistantPid()
    {
        $pid = new Pid('qqqqqqqq');

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($this->equalTo($pid))
            ->willReturn(null);

        $this->mockRepository->expects($this->never())
            ->method('findEpisodeGuideChildren');

        $expectedResult = new EntityCollectionServiceResult([], 50, 1);

        $result = $this->programmesService()->findEpisodeGuideChildrenByPid($pid);
        $this->assertEquals($expectedResult, $result);

    }

    public function testCountEpisodeGuideChildrenByPidWithNonExistantPid()
    {
        $pid = new Pid('qqqqqqqq');

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($this->equalTo($pid))
            ->willReturn(null);

        $this->mockRepository->expects($this->never())
            ->method('countEpisodeGuideChildren');

        $this->assertEquals(0, $this->programmesService()->countEpisodeGuideChildrenByPid($pid));
    }
}
