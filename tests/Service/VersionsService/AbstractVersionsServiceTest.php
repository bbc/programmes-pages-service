<?php

namespace Tests\BBC\ProgrammesPagesService\Service\VersionsService;

use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionMapper;
use BBC\ProgrammesPagesService\Service\VersionsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractVersionsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('VersionRepository');
        $this->setUpMapper(VersionMapper::class, function ($dbVersion) {
            return $this->createConfiguredMock(Version::class, ['getPid' => new Pid($dbVersion['pid'])]);
        });
    }

    protected function service()
    {
        return new VersionsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
