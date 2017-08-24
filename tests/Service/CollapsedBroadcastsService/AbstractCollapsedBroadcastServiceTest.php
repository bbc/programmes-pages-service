<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractCollapsedBroadcastServiceTest extends AbstractServiceTest
{
    protected $mockServiceRepository;

    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('CollapsedBroadcastRepository');
        $this->setUpMapper('CollapsedBroadcastMapper', function () {
            return $this->createMock(CollapsedBroadcast::class);
        });
        $this->mockServiceRepository = $this->getRepo('ServiceRepository');
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

    /**
     * @param Category[] $categories
     * @return int[]
     */
    protected function extractDbId(array $categories): array
    {
        return array_map(
            function ($category) {
                return $category->getDbId();
            },
            $categories
        );
    }
}
