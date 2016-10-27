<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

class FindByProgrammeAndMonthTest extends AbstractCollapsedBroadcastServiceTest
{
    public function testFindByProgrammeAndMonth()
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
             ->method('findByProgrammeAndMonth')
             ->with($dbAncestry, 'Broadcast', 2007, 12)
             ->willReturn($broadcastData);

        $this->mockServiceRepository->expects($this->atLeastOnce())
             ->method('findBySids')
             ->with(['a', 'b'])
             ->willReturn($serviceData);

        $result = $this->service()->findByProgrammeAndMonth($programme, 2007, 12);
        $this->assertEquals($this->collapsedBroadcastsFromDbData($broadcastData)[0], $result[0]);
    }

    public function testFindCollapsedBroadcastsByProgrammeAndMonthCustomPaginationAndLimit()
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
            ->method('findByProgrammeAndMonth')
            ->with($dbAncestry, 'Broadcast', 2007, 12, 5, 10)
            ->willReturn($broadcastData);

        $this->mockServiceRepository->expects($this->atLeastOnce())
            ->method('findBySids')
            ->with(['a', 'b'])
            ->willReturn($serviceData);

        $result = $this->service()->findByProgrammeAndMonth($programme, 2007, 12, 5, 3);
        $this->assertEquals($this->collapsedBroadcastsFromDbData($broadcastData)[0], $result[0]);

    }
}
