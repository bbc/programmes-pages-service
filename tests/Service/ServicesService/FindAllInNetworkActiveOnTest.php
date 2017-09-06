<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ServicesService;

use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use DateTimeImmutable;

class FindAllInNetworkActiveOnTest extends AbstractServicesServiceTest
{
    public function testCommunicationWithRepository()
    {
        $nid = new Nid('bbc_radio_two');
        $date = new DateTimeImmutable();

        $this->mockRepository->expects($this->once())
            ->method('findAllInNetworkActiveOn')
            ->with($nid, $date);

        $this->service()->findAllInNetworkActiveOn($nid, $date);
    }

    /**
     * @dataProvider dbServicesProvider
     */
    public function testResults(array $expectedPids, array $servicesProvided)
    {
        $this->mockRepository->method('findAllInNetworkActiveOn')->willReturn($servicesProvided);

        $services = $this->service()->findAllInNetworkActiveOn(
            $this->createMock(Nid::class),
            new DateTimeImmutable()
        );

        $this->assertCount(count($servicesProvided), $services);
        $this->assertContainsOnlyInstancesOf(Service::class, $services);
        foreach ($expectedPids as $i => $expectedPid) {
            $this->assertEquals($expectedPid, $services[$i]->getPid());
        }
    }

    public function dbServicesProvider(): array
    {
        return [
            'CASE: services are found' => [
                ['s1234567', 's2234566'],
                [['pid' => 's1234567'], ['pid' => 's2234566']],
            ],
            'CASE: services are NOT found' => [
                [],
                []
            ],
        ];
    }
}
