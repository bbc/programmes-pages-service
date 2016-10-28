<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use DateTimeImmutable;

class FindUpcomingCollapsedBroadcastsForProgrammeTest extends AbstractCollapsedBroadcastServiceTest
{
    public function testFindUpcomingByProgrammeDefaultPagination()
    {
        $dbAncestry = [1, 2, 3];
        $programme = $this->mockEntity('Programme', 3);
        $programme->method('getDbAncestryIds')->willReturn($dbAncestry);

        $broadcastData = [['serviceIds' => ['a', 'b']]];
        $serviceData = [
            'a' => ['sid' => 'bbc_one'],
            'b' => ['sid' => 'bbc_one_hd'],
        ];

        $this->mockRepository->expects($this->once())
            ->method('findUpcomingByProgramme')
            ->with($dbAncestry, 'Broadcast', $this->lessThanOrEqual(new DateTimeImmutable()), 300, 0)
            ->willReturn($broadcastData);

        $this->mockServiceRepository->expects($this->once())
            ->method('findBySids')
            ->with(['a', 'b'])
            ->willReturn($serviceData);

        $result = $this->service()->findUpcomingByProgramme($programme);
        $this->assertEquals($this->collapsedBroadcastsFromDbData($broadcastData), $result);
    }

    public function testFindUpcomingByProgrammeCustomPagination()
    {
        $dbAncestry = [1, 2, 3];
        $programme = $this->mockEntity('Programme', 3);
        $programme->method('getDbAncestryIds')->willReturn($dbAncestry);

        $broadcastData = [['serviceIds' => ['a', 'b']]];
        $serviceData = [
            'a' => ['sid' => 'bbc_one'],
            'b' => ['sid' => 'bbc_one_hd'],
        ];

        $this->mockRepository->expects($this->once())
            ->method('findUpcomingByProgramme')
            ->with($dbAncestry, 'Broadcast', $this->lessThanOrEqual(new DateTimeImmutable()), 5, 10)
            ->willReturn($broadcastData);

        $this->mockServiceRepository->expects($this->once())
            ->method('findBySids')
            ->with(['a', 'b'])
            ->willReturn($serviceData);

        $result = $this->service()->findUpcomingByProgramme($programme, 5, 3);
        $this->assertEquals($this->collapsedBroadcastsFromDbData($broadcastData), $result);
    }

    public function testCountUpcomingByProgramme()
    {
        $dbAncestry = [1, 2, 3];
        $programme = $this->mockEntity('Programme');
        $programme->method('getDbAncestryIds')->willReturn($dbAncestry);

        $this->mockRepository->expects($this->once())
            ->method('countUpcomingByProgramme')
            ->with($dbAncestry, 'Broadcast', $this->lessThanOrEqual(new DateTimeImmutable()))
            ->willReturn(10);

        $this->mockServiceRepository->expects($this->never())
            ->method('findBySids');

        $this->assertEquals(10, $this->service()->countUpcomingByProgramme($programme));
    }

    public function testFindUpcomingByProgrammeWithNonExistantPid()
    {
        $dbAncestry = [997, 998, 999];
        $programme = $this->mockEntity('Programme');
        $programme->method('getDbAncestryIds')->willReturn($dbAncestry);

        $this->mockRepository->expects($this->once())
            ->method('findUpcomingByProgramme')
            ->with($dbAncestry, 'Broadcast', $this->lessThanOrEqual(new DateTimeImmutable()), 5, 10)
            ->willReturn([]);

        $this->mockServiceRepository->expects($this->never())
            ->method('findBySids');

        $result = $this->service()->findUpcomingByProgramme($programme, 5, 3);
        $this->assertEquals([], $result);
    }

    public function testCountUpcomingByProgrammeWithNonExistantPid()
    {
        $dbAncestry = [997, 998, 999];
        $programme = $this->mockEntity('Programme');
        $programme->method('getDbAncestryIds')->willReturn($dbAncestry);

        $this->mockRepository->expects($this->once())
            ->method('countUpcomingByProgramme')
            ->with($dbAncestry, 'Broadcast', $this->lessThanOrEqual(new DateTimeImmutable()))
            ->willReturn(0);

        $this->mockServiceRepository->expects($this->never())
            ->method('findBySids');

        $this->assertEquals(0, $this->service()->countUpcomingByProgramme($programme));
    }
}
