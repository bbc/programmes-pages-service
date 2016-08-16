<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributionsService;

class FindByContributionToSegmentTest extends AbstractContributionsServiceTest
{
    public function testFindByContributionToSegmentDefaultPagination()
    {
        $dbId = 1;
        $segment = $this->mockEntity('Segment', $dbId);
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$dbId], 'segment', 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findByContributionToSegment($segment);
        $this->assertEquals($this->contributionsFromDbData($dbData), $result);
    }

    public function testFindByContributionToSegmentCustomPagination()
    {
        $dbId = 1;
        $segment = $this->mockEntity('Segment', $dbId);
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$dbId], 'segment', 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findByContributionToSegment($segment, 5, 3);
        $this->assertEquals($this->contributionsFromDbData($dbData), $result);
    }

    public function testFindByContributionToSegmentWithNonExistantDbId()
    {
        $dbId = 999;
        $segment = $this->mockEntity('Segment', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$dbId], 'segment', 5, 10)
            ->willReturn([]);

        $result = $this->service()->findByContributionToSegment($segment, 5, 3);
        $this->assertEquals([], $result);
    }
}
