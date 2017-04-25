<?php

namespace Tests\BBC\ProgrammesPagesService\Service\VersionsService;

use BBC\ProgrammesPagesService\Service\VersionsService;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractVersionsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpRepo('VersionRepository');
        $this->setUpMapper('VersionMapper', 'versionFromDbData');
    }

    protected function versionsFromDbData(array $entities)
    {
        return array_map([$this, 'versionFromDbData'], $entities);
    }

    protected function versionFromDbData(array $entity)
    {
        $mockVersion = $this->createMock(self::ENTITY_NS . 'Version');
        $mockVersion->method('getPid')->willReturn($entity['pid']);
        return $mockVersion;
    }

    protected function service()
    {
        return new VersionsService($this->mockRepository, $this->mockMapper, new NullAdapter());
    }
}
