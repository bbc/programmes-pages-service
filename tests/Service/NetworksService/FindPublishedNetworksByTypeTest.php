<?php

namespace Tests\BBC\ProgrammesPagesService\Service\NetworksService;

class FindPublishedNetworksByTypeTest extends AbstractNetworksServiceTest
{
    public function testFindPublishedNetworksByTypeDefaultPagination()
    {
        $dbData = [['nid' => 'bbc_one'], ['nid' => 'bbc_two']];

        $this->mockRepository->expects($this->once())
            ->method('findPublishedNetworksByType')
            ->with(['TV'], 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findPublishedNetworksByType(['TV']);
        $this->assertEquals($this->networksFromDbData($dbData), $result);
    }

    public function testFindPublishedNetworksByTypeCustomPagination()
    {
        $dbData = [['nid' => 'bbc_one'], ['nid' => 'bbc_two']];

        $this->mockRepository->expects($this->once())
            ->method('findPublishedNetworksByType')
            ->with(['TV'], 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findPublishedNetworksByType(['TV'], 5, 3);
        $this->assertEquals($this->networksFromDbData($dbData), $result);
    }
}
