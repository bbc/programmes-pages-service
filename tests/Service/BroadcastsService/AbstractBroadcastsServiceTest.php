<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\BroadcastsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractBroadcastsServiceTest extends AbstractServiceTest
{
    public function setUp(): void
    {
        $this->setUpCache();
        $this->setUpRepo('BroadcastRepository');
        $this->setUpMapper('BroadcastMapper', 'broadcastFromDbData');
    }

    /**
     * @param array[] $dbData
     */
    protected function broadcastFromDbData(array $dbData): Broadcast
    {
        $entity = $this->createMock(Broadcast::class);
        $entity->method('getPid')->willReturn(new Pid($dbData['pid']));
        return $entity;
    }

    protected function service(): BroadcastsService
    {
        return new BroadcastsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
