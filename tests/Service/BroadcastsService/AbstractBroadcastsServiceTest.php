<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Service\BroadcastsService;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractBroadcastsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
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
        $mockBroadcast->method('getPid')->willReturn($entity['pid']);
        return $mockBroadcast;
    }

    protected function service()
    {
        return new BroadcastsService($this->mockRepository, $this->mockMapper, new NullAdapter());
    }
}
