<?php

namespace Tests\BBC\ProgrammesPagesService\Service\AtozTitlesService;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\AtozTitleRepository;
use BBC\ProgrammesPagesService\Domain\Entity\AtozTitle;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\AtozTitleMapper;
use BBC\ProgrammesPagesService\Service\AtozTitlesService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractAtozTitlesServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo(AtozTitleRepository::class);
        $this->setUpMapper(AtozTitleMapper::class, function (array $dbData) {
            return $this->createConfiguredMock(
                AtozTitle::class,
                ['getFirstletter' => $dbData['firstLetter']]
            );
        });
    }

    protected function service(): AtozTitlesService
    {
        return new AtozTitlesService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }

    /**
     * @param AtozTitle[] $atozTitles any model domain with getPid() function
     * @return string[] with only the firstLetter property of each object
     */
    protected function extractFirstLetter(array $atozTitles): array
    {
        return array_map(
            function ($atozTitle) {
                return $atozTitle->getFirstletter();
            },
            $atozTitles
        );
    }
}
