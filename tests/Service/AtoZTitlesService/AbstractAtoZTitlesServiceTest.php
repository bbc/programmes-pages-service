<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtoZTitlesService;

use BBC\ProgrammesPagesService\Service\AtoZTitlesService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractAtoZTitlesServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpRepo('AtoZTitleRepository');
        $this->setUpMapper('AtoZTitleMapper', 'atoZTitleFromDbData');
    }

    protected function atoZTitlesFromDbData(array $entities)
    {
        return array_map([$this, 'atoZTitleFromDbData'], $entities);
    }

    protected function atoZTitleFromDbData(array $entity)
    {
        $mockAtoZTitle = $this->createMock(self::ENTITY_NS . 'AtoZTitle');
        $mockAtoZTitle->method('getTitle')->willReturn($entity['title']);
        return $mockAtoZTitle;
    }

    protected function service()
    {
        return new AtoZTitlesService($this->mockRepository, $this->mockMapper);
    }
}
