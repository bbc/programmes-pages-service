<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;

use BBC\ProgrammesPagesService\Domain\Enumeration\CategoryTypeEnum;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository::<public>
 */
class FindUsedByTypeTest extends AbstractDatabaseTest
{
    public function testFindByIds()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entities = $repo->findUsedByType(CategoryTypeEnum::GENRE);
        $this->assertSame(['C00193', 'C00196'], array_column($entities, 'pipId'));

        // findUsedByType query only
        $this->assertCount(2, $this->getDbQueries());
    }

    public function testFindByIdsFullWhenEmptyResult()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entities = $repo->findUsedByType('thing');
        $this->assertSame([], $entities);

        // findUsedByType query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
