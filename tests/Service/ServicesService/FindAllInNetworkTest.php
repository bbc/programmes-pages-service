<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ServicesService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;

class FindAllInNetworkTest extends AbstractServicesServiceTest
{
    public function testFindAllInNetwork()
    {
        $nid = new Nid('bbc_radio_two');
        $dbData = [['pid' => '234']];

        $this->mockRepository->expects($this->once())
            ->method('findAllInNetwork')
            ->with($nid)
            ->willReturn($dbData);

        $servicesInNetwork = $this->service()->findAllInNetwork($nid);

        $this->assertEquals($this->servicesFromDbData($dbData), $servicesInNetwork);
    }
}
