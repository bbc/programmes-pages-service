<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

class FindByProgrammeAndMonthTest extends AbstractCollapsedBroadcastServiceTest
{
    public function testFindByProgrammeAndMonthDefaultPagination()
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
            ->method('findByProgrammeAndMonth')
            ->with($dbAncestry, false, 2007, 12)
            ->willReturn($broadcastData);

        $this->mockServiceRepository->expects($this->atLeastOnce())
            ->method('findByIds')
            ->with(['a', 'b'])
            ->willReturn($serviceData);

        $result = $this->service()->findByProgrammeAndMonth($programme, 2007, 12);
        $this->assertEquals($this->collapsedBroadcastsFromDbData($broadcastData), $result);
    }

    public function testFindByProgrammeAndMonthCustomPagination()
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
            ->method('findByProgrammeAndMonth')
            ->with($dbAncestry, false, 2007, 12, 5, 10)
            ->willReturn($broadcastData);

        $this->mockServiceRepository->expects($this->atLeastOnce())
            ->method('findByIds')
            ->with(['a', 'b'])
            ->willReturn($serviceData);

        $result = $this->service()->findByProgrammeAndMonth($programme, 2007, 12, 5, 3);
        $this->assertEquals($this->collapsedBroadcastsFromDbData($broadcastData), $result);
    }

    public function testFindByProgrammeAndMonthWithNonExistantPid()
    {
        $dbAncestry = [997, 998, 999];
        $programme = $this->mockEntity('Programme');
        $programme->method('getDbAncestryIds')->willReturn($dbAncestry);

        $this->mockRepository->expects($this->once())
            ->method('findByProgrammeAndMonth')
            ->with($dbAncestry, false, 2007, 12, 5, 10)
            ->willReturn([]);

        $this->mockServiceRepository->expects($this->never())
            ->method('findByIds');

        $result = $this->service()->findByProgrammeAndMonth($programme, 2007, 12, 5, 3);
        $this->assertEquals([], $result);
    }
}
