<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

use BBC\ProgrammesPagesService\Service\AtozTitlesService;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractAtozTitlesServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpRepo('AtozTitleRepository');
        $this->setUpMapper('AtozTitleMapper', 'atoZTitleFromDbData');
    }

    protected function atoZTitlesFromDbData(array $entities)
    {
        return array_map([$this, 'atoZTitleFromDbData'], $entities);
    }

    protected function atoZTitleFromDbData(array $entity)
    {
        $mockAtozTitle = $this->createMock(self::ENTITY_NS . 'AtozTitle');
        $mockAtozTitle->method('getTitle')->willReturn($entity['title']);
        return $mockAtozTitle;
    }

    protected function service()
    {
        return new AtozTitlesService($this->mockRepository, $this->mockMapper, new NullAdapter());
    }
}
