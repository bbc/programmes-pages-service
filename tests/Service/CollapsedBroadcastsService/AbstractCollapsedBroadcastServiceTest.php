<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractCollapsedBroadcastServiceTest extends AbstractServiceTest
{
    protected $mockServiceRepository;

    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('CollapsedBroadcastRepository');
        $this->setUpMapper('CollapsedBroadcastMapper', 'collapsedBroadcastFromDbData');
        $this->mockServiceRepository = $this->getRepo('ServiceRepository');
    }

    protected function collapsedBroadcastsFromDbData(array $entities)
    {
        return array_map([$this, 'collapsedBroadcastFromDbData'], $entities);
    }

    protected function collapsedBroadcastFromDbData(array $entity)
    {
        return $this->createMock(self::ENTITY_NS . 'CollapsedBroadcast');
    }

    protected function service(): CollapsedBroadcastsService
    {
        return new CollapsedBroadcastsService(
            $this->mockRepository,
            $this->mockMapper,
            $this->mockCache,
            $this->mockServiceRepository
        );
    }
}
