<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributionsService;

class FindByContributionToSegmentsTest extends AbstractContributionsServiceTest
{
    public function testFindByContributionToSegmentsDefaultPagination()
    {
        $dbIds = [1, 2, 3];
        $dbData = [['pid' => 'b0000001']];
        $segments = [
            $this->mockEntity('Segment', 1),
            $this->mockEntity('Segment', 2),
            $this->mockEntity('Segment', 3),
        ];

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with($dbIds, 'segment', true, 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findByContributionToSegments($segments);
        $this->assertEquals($this->contributionsFromDbData($dbData), $result);
    }

    public function testFindByContributionToSegmentsCustomPagination()
    {
        $dbIds = [1, 2, 3];
        $dbData = [['pid' => 'b0000001']];
        $segments = [
            $this->mockEntity('Segment', 1),
            $this->mockEntity('Segment', 2),
            $this->mockEntity('Segment', 3),
        ];

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with($dbIds, 'segment', true, 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findByContributionToSegments($segments, 5, 3);
        $this->assertEquals($this->contributionsFromDbData($dbData), $result);
    }

    public function testFindByContributionToSegmentsWithNonExistantDbId()
    {
        $dbIds = [1, 2, 3];
        $segments = [
            $this->mockEntity('Segment', 1),
            $this->mockEntity('Segment', 2),
            $this->mockEntity('Segment', 3),
        ];

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with($dbIds, 'segment', true, 5, 10)
            ->willReturn([]);

        $result = $this->service()->findByContributionToSegments($segments, 5, 3);
        $this->assertEquals([], $result);
    }
}
