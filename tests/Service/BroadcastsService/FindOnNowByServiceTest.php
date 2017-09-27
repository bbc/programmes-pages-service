<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Service;

class FindOnNowByServiceTest extends AbstractBroadcastsServiceTest
{
    public function testFindOnNowByServiceResults()
    {
        $service = $this->createConfiguredMock(Service::class, ['getDbId' => 1]);

        $this->mockRepository
            ->method('findOnNowByService')
            ->with(1)
            ->willReturn(['pid' => 'b00swyx1']);

        /** @var $broadcast Broadcast This mock shall always return a Broadcast, never null */
        $broadcast = $this->service()->findOnNowByService($service);

        $this->assertInstanceOf(Broadcast::class, $broadcast);
        $this->assertEquals('b00swyx1', $broadcast->getPid());
    }

    public function testFindOnNowByServiceEmptyResults()
    {
        $this->mockRepository
            ->method('findOnNowByService')
            ->willReturn(null);

        $broadcast = $this->service()->findOnNowByService(
            $this->createMock(Service::class)
        );

        $this->assertSame(null, $broadcast);
    }
}
