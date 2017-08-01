<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use DateTimeImmutable;

class FindPastByProgrammeTest extends AbstractCollapsedBroadcastServiceTest
{
    public function testFindPastByProgrammeDefaultPagination()
    {
        $dbAncestry = [1, 2, 3];
        $programme = $this->mockEntity('Programme', 3);
        $programme->method('getDbAncestryIds')->willReturn($dbAncestry);

        $broadcastData = [['areWebcasts' => ['0'], 'serviceIds' => ['a', 'b']]];
        $serviceData = [
            'a' => ['id' => 'bbc_one'],
            'b' => ['id' => 'bbc_one_hd'],
        ];

        $this->mockRepository->expects($this->once())
            ->method('findPastByProgramme')
            ->with($dbAncestry, false, $this->lessThanOrEqual(new DateTimeImmutable()), 300, 0)
            ->willReturn($broadcastData);

        $this->mockServiceRepository->expects($this->atLeastOnce())
            ->method('findByIds')
            ->with(['a', 'b'])
            ->willReturn($serviceData);

        $result = $this->service()->findPastByProgramme($programme);
        $this->assertEquals(count($result), 1);
        $this->assertEquals($this->collapsedBroadcastsFromDbData($broadcastData), $result);
    }

    public function testFindPastByProgrammeCustomPagination()
    {
        $dbAncestry = [1, 2, 3];
        $programme = $this->mockEntity('Programme', 3);
        $programme->method('getDbAncestryIds')->willReturn($dbAncestry);

        $broadcastData = [['areWebcasts' => ['0'], 'serviceIds' => ['a', 'b']]];
        $serviceData = [
            'a' => ['id' => 'bbc_one'],
            'b' => ['id' => 'bbc_one_hd'],
        ];

        $this->mockRepository->expects($this->once())
            ->method('findPastByProgramme')
            ->with($dbAncestry, false, $this->lessThanOrEqual(new DateTimeImmutable()), 5, 10)
            ->willReturn($broadcastData);

        $this->mockServiceRepository->expects($this->atLeastOnce())
            ->method('findByIds')
            ->with(['a', 'b'])
            ->willReturn($serviceData);

        $result = $this->service()->findPastByProgramme($programme, 5, 3);
        $this->assertEquals(count($result), 1);
        $this->assertEquals($this->collapsedBroadcastsFromDbData($broadcastData), $result);
    }

    public function testFindPastByProgrammeWithNonExistantPid()
    {
        $dbAncestry = [997, 998, 999];
        $programme = $this->mockEntity('Programme');
        $programme->method('getDbAncestryIds')->willReturn($dbAncestry);

        $this->mockRepository->expects($this->once())
            ->method('findPastByProgramme')
            ->with($dbAncestry, false, $this->lessThanOrEqual(new DateTimeImmutable()), 5, 10)
            ->willReturn([]);

        $this->mockServiceRepository->expects($this->never())
            ->method('findByIds');

        $result = $this->service()->findPastByProgramme($programme, 5, 3);
        $this->assertEquals([], $result);
    }
}
