<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\BroadcastsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractBroadcastsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('BroadcastRepository');
        $this->setUpMapper('BroadcastMapper', 'broadcastFromDbData');
    }

    protected function broadcastsFromDbData(array $entities)
    {
        return array_map([$this, 'broadcastFromDbData'], $entities);
    }

    protected function broadcastFromDbData(array $entity)
    {
        $mockBroadcast = $this->createMock(self::ENTITY_NS . 'Broadcast');
        $mockBroadcast->method('getPid')->willReturn(new Pid($entity['pid']));
        return $mockBroadcast;
    }

    protected function service()
    {
        return new BroadcastsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
