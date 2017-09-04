<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use BBC\ProgrammesPagesService\Service\CollapsedBroadcastsService;
use BBC\ProgrammesPagesService\Domain\Entity\Category;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractCollapsedBroadcastServiceTest extends AbstractServiceTest
{
    protected $mockServiceRepository;

    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('CollapsedBroadcastRepository');
        $this->setUpMapper('CollapsedBroadcastMapper', function (array $dbDataBroadcast, array $dbDataServices) {
            $stubServices = array_map(
                function ($dbDataService) {
                    return $this->createConfiguredMock(Service::class, [
                        'getDbId' => $dbDataService['id'],
                        'getSid' => new Sid($dbDataService['sid']),
                    ]);
                },
                $dbDataServices
            );

            return $this->createConfiguredMock(CollapsedBroadcast::class, [
                'getServices' => $stubServices,
            ]);
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
    protected function extractDbAncestryIds(array $categories): array
    {
        return array_map(
            function ($category) {
                return $category->getDbAncestryIds();
            },
            $categories
        );
    }
}
