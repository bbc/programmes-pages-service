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
        $mockService = $this->createMock(self::ENTITY_NS . 'Service');
        $mockService->method('getPid')->willReturn(
            $entity['pid']
        );
        return $mockService;
    }

    protected function service()
    {
        return new ServicesService($this->mockRepository, $this->mockMapper);
    }
}
