<?php

namespace Tests\BBC\ProgrammesPagesService\Service\NetworksService;

use BBC\ProgrammesPagesService\Service\NetworksService;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractNetworksServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpRepo('NetworkRepository');
        $this->setUpMapper('NetworkMapper', 'networkFromDbData');
    }

    protected function networksFromDbData(array $entities)
    {
        return array_map([$this, 'networkFromDbData'], $entities);
    }

    protected function networkFromDbData(array $entity)
    {
        $mockNetwork = $this->createMock(self::ENTITY_NS . 'Network');
        return $mockNetwork;
    }

    protected function service()
    {
        return new NetworksService($this->mockRepository, $this->mockMapper, new NullAdapter());
    }
}
