<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Service\CategoriesService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractCategoriesServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('CategoryRepository');
        $this->setUpMapper('CategoryMapper', function ($dbData) {
            $className = substr($dbData['pip_id'], 0, 1) === 'C' ? Genre::class : Format::class;
            return $this->createConfiguredMock(
                $className,
                ['getId' => $dbData['pip_id']]
            );
        });
    }


    protected function service(): CategoriesService
    {
        return new CategoriesService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }

    /**
     * @param Categories[] $categories
     * @return string[] containing only the genres ids
     */
    protected function extractIds(array $categories): array
    {
        return array_map(
            function ($category){
                return $category->getId();
            },
            $categories
        );
    }
}
