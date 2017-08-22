<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CategoriesService;

use BBC\ProgrammesPagesService\Service\CategoriesService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractCategoriesServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('CategoryRepository');
        $this->setUpMapper('CategoryMapper', 'categoryFromDbData');
    }

    protected function categoryFromDbData(array $entity)
    {
        $type = substr($entity['pip_id'], 0, 1) === 'C' ? 'Genre' : 'Format';
        $mockCategory = $this->createMock(self::ENTITY_NS . $type);
        $mockCategory->method('getId')->willReturn($entity['pip_id']);
        return $mockCategory;
    }

    protected function service()
    {
        return new CategoriesService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
