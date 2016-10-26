<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

class FindPastCollapsedBroadcastsForProgrammeTest extends AbstractCollapsedBroadcastServiceTest
{
    public function testFindCollapsedBroadcastsByProgrammeAndMonth()
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
            ->method('findPastCollapsedBroadcastsForProgramme')
            ->with($dbAncestry, 'Broadcast', 1, 0)
            ->willReturn($broadcastData);

        $this->mockServiceRepository->expects($this->atLeastOnce())
            ->method('findBySids')
            ->with(['a', 'b'])
            ->willReturn($serviceData);

        $result = $this->service()->findPastCollapsedBroadcastsForProgramme($programme, 1);
        $this->assertEquals(count($result), 1);
        $this->assertEquals($this->collapsedBroadcastsFromDbData($broadcastData), $result);
    }
}
