<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentEventsService;

class FindByVersionTest extends AbstractSegmentEventsServiceTest
{
    public function testFindByVersionDefaultPagination()
    {
        $dbId = 1;
        $version = $this->mockEntity('Version', $dbId);
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findByVersion')
            ->with([$dbId], 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findByVersion($version);
        $this->assertEquals($this->segmentEventsFromDbData($dbData), $result);
    }

    public function testFindByVersionCustomPagination()
    {
        $dbId = 1;
        $version = $this->mockEntity('Version', $dbId);
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findByVersion')
            ->with([$dbId], 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findByVersion($version, 5, 3);
        $this->assertEquals($this->segmentEventsFromDbData($dbData), $result);
    }

    public function testFindByVersionWithNonExistantDbId()
    {
        $dbId = 999;
        $version = $this->mockEntity('Version', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findByVersion')
            ->with([$dbId], 5, 10)
            ->willReturn([]);

        $result = $this->service()->findByVersion($version, 5, 3);
        $this->assertEquals([], $result);
    }
}
