<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

use BBC\ProgrammesPagesService\Service\AtozTitlesService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;
use BBC\ProgrammesPagesService\Domain\Entity\AtozTitle;

abstract class AbstractAtozTitlesServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('AtozTitleRepository');
        $this->setUpMapper('AtozTitleMapper', 'atoZTitleFromDbData');
    }

    protected function atoZTitleFromDbData(array $entity): AtozTitle
    {
        $mockAtozTitle = $this->createMock(AtozTitle::class);
        $mockAtozTitle->method('getTitle')->willReturn($entity['title']);
        return $mockAtozTitle;
    }

    protected function service(): AtozTitlesService
    {
        return new AtozTitlesService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
