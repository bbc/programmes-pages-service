<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ServicesService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\ServicesService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractServicesServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpRepo('ServiceRepository');
        $this->setUpMapper('ServiceMapper', 'serviceFromDbData');
    }

    protected function servicesFromDbData(array $entities)
    {
        return array_map([$this, 'serviceFromDbData'], $entities);
    }

    protected function serviceFromDbData($entity)
    {
        $mockVersion = $this->createMock(self::ENTITY_NS . 'Service');
        $mockVersion->method('getPid')->willReturn(
            new Pid($entity->getPid())
        );
        return $mockVersion;
    }

    protected function service()
    {
        return new ServicesService($this->mockRepository, $this->mockMapper);
    }
}
