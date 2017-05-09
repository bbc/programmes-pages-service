<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ServicesService;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindAllInNetworkTest extends AbstractServicesServiceTest
{
    public function testFindAllInNetwork()
    {
        $nid = new Nid('bbc_radio_two');
        $dbData = [
            [
                'id' => '34234234',
                'sid'=>'asdfasdf',
                'pid'=>'234',
                'name'=>'anyname',
                'shortName'=>'anyshortname',
                'urlKey'=>'urlKey',
                'liveStreamUrl'=> null
            ]
        ];

        $this->mockRepository->expects($this->once())
                             ->method('findAllInNetwork')
                             ->with($nid)
                             ->willReturn($dbData);

        $servicesInNetwork = $this->service()->findAllInNetwork($nid);

        $this->assertInternalType('array', $servicesInNetwork);
        $this->assertCount(1, $servicesInNetwork);

        array_map(
            function($serviceInNetwork)
            {
                $this->assertInstanceOf(
                    'BBC\ProgrammesPagesService\Domain\Entity\Service',
                    $serviceInNetwork
                );
            },

            $servicesInNetwork
        );
    }
}
