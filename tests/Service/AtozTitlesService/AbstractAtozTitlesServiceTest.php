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

    protected function service(): AtozTitlesService
    {
        return new AtozTitlesService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }

    /**
     * @param $tleosTitles[] $tleosTitles any model domain with getPid() function
     * @return string[] with only the firstLetter property of each object
     */
    protected function extractFirstLetter(array $tleosTitles): array
    {
        return array_map(
            function ($entity) {
                return $entity->getFirstletter();
            },
            $tleosTitles
        );
    }
}
