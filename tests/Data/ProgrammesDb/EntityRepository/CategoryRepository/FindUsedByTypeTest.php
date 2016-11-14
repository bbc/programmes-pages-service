<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository::<public>
 */
class FindUsedByTypeTest extends AbstractDatabaseTest
{
    public function testFindUsedByType()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entities = $repo->findUsedByType('genre');
        $this->assertSame(['C00193', 'C00196'], array_column($entities, 'pipId'));

        // findUsedByType query only
        $this->assertCount(2, $this->getDbQueries());
    }

    public function testFindUsedByTypeWhenEmptyResult()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entities = $repo->findUsedByType('thing');
        $this->assertSame([], $entities);

        // findUsedByType query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
