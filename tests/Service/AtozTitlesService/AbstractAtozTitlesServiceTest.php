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
        $this->setUpMapper('AtozTitleMapper', function(array $dbData) {
            $stubAtozTitle = $this->createMock(AtozTitle::class);
            $stubAtozTitle->method('getFirstletter')->willReturn($dbData['firstLetter']);
            return $stubAtozTitle;
        });
    }

    /**
     * @param array $entities any model domain with getPid() function
     * @return string[]
     */
    protected function extractFirstLetter(array $entities): array
    {
        return array_map(
            function ($entity) {
                return $entity->getFirstletter();
            },
            $entities
        );
    }

    protected function service(): AtozTitlesService
    {
        return new AtozTitlesService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
