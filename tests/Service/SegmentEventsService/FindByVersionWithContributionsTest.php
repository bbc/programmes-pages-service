<?php

namespace Tests\BBC\ProgrammesPagesService\Service\SegmentEventsService;

class FindByVersionWithContributionsTest extends AbstractSegmentEventsServiceTest
{
    public function testFindByVersionWithContributionsDefaultPagination()
    {
        $dbId = 1;
        $version = $this->mockEntity('Version', $dbId);
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findByVersionWithContributions')
            ->with([$dbId], 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findByVersionWithContributions($version);
        $this->assertEquals($this->segmentEventsFromDbData($dbData), $result);
    }

    public function testFindByVersionWithContributionsCustomPagination()
    {
        $dbId = 1;
        $version = $this->mockEntity('Version', $dbId);
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findByVersionWithContributions')
            ->with([$dbId], 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findByVersionWithContributions($version, 5, 3);
        $this->assertEquals($this->segmentEventsFromDbData($dbData), $result);
    }

    public function testFindByVersionWithContributionsWithNonExistantDbId()
    {
        $dbId = 999;
        $version = $this->mockEntity('Version', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findByVersionWithContributions')
            ->with([$dbId], 5, 10)
            ->willReturn([]);

        $result = $this->service()->findByVersionWithContributions($version, 5, 3);
        $this->assertEquals([], $result);
    }
}
