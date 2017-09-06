<?php

namespace Tests\BBC\ProgrammesPagesService\Service\NetworksService;

use BBC\ProgrammesPagesService\Domain\Entity\Network;

class FindByUrlKeyWithDefaultServiceTest extends AbstractNetworksServiceTest
{
    public function testProtocolWithDatabaseInterface()
    {
        $urlKey = 'radio2';

        $this->mockRepository->expects($this->once())
            ->method('findByUrlKeyWithDefaultService')
            ->with($urlKey);

        $this->service()->findByUrlKeyWithDefaultService($urlKey);
    }

    public function testNetworksResultsAreReceived()
    {
        $this->mockRepository->method('findByUrlKeyWithDefaultService')->willReturn(['nid' => 'bbc_radio_two']);

        $network = $this->service()->findByUrlKeyWithDefaultService('radio2');

        $this->assertInstanceOf(Network::class, $network);
        $this->assertEquals('bbc_radio_two', $network->getNid());
    }

    public function testNetworksResultsAreNotFound()
    {
        $this->mockRepository->method('findByUrlKeyWithDefaultService')->willReturn(null);

        $result = $this->service()->findByUrlKeyWithDefaultService('radionope');

        $this->assertNull($result);
    }
}
