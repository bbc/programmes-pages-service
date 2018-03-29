<?php

namespace Tests\BBC\ProgrammesPagesService\Service\NetworksService;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\NetworkRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\NetworkMapper;
use BBC\ProgrammesPagesService\Service\NetworksService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractNetworksServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo(NetworkRepository::class);
        $this->setUpMapper(NetworkMapper::class, function ($dbNetwork) {
            return $this->createConfiguredMock(Network::class, ['getNid' => new Nid($dbNetwork['nid'])]);
        });
    }

    protected function service()
    {
        return new NetworksService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
