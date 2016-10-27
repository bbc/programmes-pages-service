<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use DateTimeImmutable;

class FindPastCollapsedBroadcastsForProgrammeTest extends AbstractCollapsedBroadcastServiceTest
{
    public function testFindPastByProgramme()
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
            ->method('findPastByProgramme')
            ->with($dbAncestry, 'Broadcast', new DateTimeImmutable(), 300, 0)
            ->willReturn($broadcastData);

        $this->mockServiceRepository->expects($this->atLeastOnce())
            ->method('findBySids')
            ->with(['a', 'b'])
            ->willReturn($serviceData);

        $result = $this->service()->findPastByProgramme($programme);
        $this->assertEquals(count($result), 1);
        $this->assertEquals($this->collapsedBroadcastsFromDbData($broadcastData), $result);
    }

    public function testFindPastByProgrammeCustomPaginationAndLimit()
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
            ->method('findPastByProgramme')
            ->with($dbAncestry, 'Broadcast', new DateTimeImmutable(), 1, 2)
            ->willReturn($broadcastData);

        $this->mockServiceRepository->expects($this->atLeastOnce())
            ->method('findBySids')
            ->with(['a', 'b'])
            ->willReturn($serviceData);

        $result = $this->service()->findPastByProgramme($programme, 1, 3);
        $this->assertEquals(count($result), 1);
        $this->assertEquals($this->collapsedBroadcastsFromDbData($broadcastData), $result);
    }
}
