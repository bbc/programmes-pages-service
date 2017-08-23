<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\BroadcastsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractBroadcastsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('BroadcastRepository');
        $this->setUpMapper('BroadcastMapper', function (array $dbData) {
            $stubBroadcast = $this->createMock(Broadcast::class);
            $stubBroadcast->method('getPid')->willReturn(new Pid($dbData['pid']));
            return $stubBroadcast;
        });
    }

    protected function service(): BroadcastsService
    {
        return new BroadcastsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
