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
        $stubAtozTitle = $this->createMock(AtozTitle::class);
        $stubAtozTitle->method('getFirstletter')->willReturn($entity['firstLetter']);
        return $stubAtozTitle;
    }

    protected function service(): AtozTitlesService
    {
        return new AtozTitlesService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
