<?php

namespace Tests\BBC\ProgrammesPagesService\Service\NetworksService;

use BBC\ProgrammesPagesService\Domain\Entity\Network;

class FindPublishedNetworksByTypeTest extends AbstractNetworksServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testPagination(int $expectedLimit, int $expectedOffset, array $paramsPagination)
    {
        $this->mockRepository->expects($this->once())
             ->method('findPublishedNetworksByType')
             ->with(['TV'], $expectedLimit, $expectedOffset);

        $this->service()->findPublishedNetworksByType(['TV'], ...$paramsPagination);
    }

    public function paginationProvider()
    {
        return [
            // [expectedLimit, expectedOffset, [limit, page]]
            'default pagination' => [300, 0, []],
            'custom pagination' => [3, 12, [3, 5]],
        ];
    }

    public function testCanRecievePublishedNetworks()
    {
        $this->mockRepository
            ->method('findPublishedNetworksByType')
            ->willReturn([['nid' => 'bbc_one'], ['nid' => 'bbc_two']]);

        $publishedNetworks = $this->service()->findPublishedNetworksByType(['TV']);

        $this->assertContainsOnly(Network::class, $publishedNetworks);
        $this->assertEquals('bbc_one', (string) $publishedNetworks[0]->getNid());
        $this->assertEquals('bbc_two', (string) $publishedNetworks[1]->getNid());
    }

    public function testNoFoundResults()
    {
        $this->mockRepository
            ->method('findPublishedNetworksByType')
            ->willReturn([]);

        $publishedNetworks = $this->service()->findPublishedNetworksByType(['TV']);

        $this->assertEquals([], $publishedNetworks);
    }
}
