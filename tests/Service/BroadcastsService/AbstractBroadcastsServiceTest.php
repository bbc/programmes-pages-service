<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\BroadcastMapper;
use BBC\ProgrammesPagesService\Service\BroadcastsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractBroadcastsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('BroadcastRepository');
        $this->setUpMapper(BroadcastMapper::class, function (array $dbData) {
            return $this->createConfiguredMock(
                Broadcast::class,
                ['getPid' => new Pid($dbData['pid'])]
            );
        });
    }

    protected function service(): BroadcastsService
    {
        return new BroadcastsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
