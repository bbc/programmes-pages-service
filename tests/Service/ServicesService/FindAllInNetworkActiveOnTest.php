<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ServicesService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;

class FindAllInNetworkActiveOnTest extends AbstractServicesServiceTest
{
    public function testFindAllInNetworkActiveOn()
    {
        $nid = new Nid('bbc_radio_two');
        $dbData = [['pid' => '234']];

        $date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2010-01-15 06:00:00');
        $this->mockRepository->expects($this->once())
            ->method('findAllInNetworkActiveOn')
            ->with($nid, $date)
            ->willReturn($dbData);

        $servicesInNetwork = $this->service()->findAllInNetworkActiveOn($nid, $date);

        $this->assertEquals($this->servicesFromDbData($dbData), $servicesInNetwork);
    }
}
