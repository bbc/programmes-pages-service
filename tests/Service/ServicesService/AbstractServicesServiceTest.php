<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ServicesService;

use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ServiceMapper;
use BBC\ProgrammesPagesService\Service\ServicesService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractServicesServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('ServiceRepository');
        $this->setUpMapper(ServiceMapper::class, function ($dbService) {
            return $this->createConfiguredMock(Service::class, ['getPid' => new Pid($dbService['pid'])]);
        });
    }

    protected function service()
    {
        return new ServicesService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
