<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository::<public>
 */
class FindAllDescendantsByParentIdTest extends AbstractDatabaseTest
{
    public function testFindPopulatedChildCategories()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $children = $repo->findAllDescendantsByParentId(
            $dbId = $this->getDbIdFromPersistentIdentifier('C00193', 'Category', 'pipId'),
            'genre'
        );
        $this->assertCount(1, $children);

        $childCategory = $children[0];
        $this->assertEquals($this->getDbIdFromPersistentIdentifier('C00196', 'Category', 'pipId'), $childCategory['id']);
        $this->assertEquals('sitcoms', $childCategory['urlKey']);

        $this->assertCount(1, $childCategory['children']);
        $this->assertEquals($this->getDbIdFromPersistentIdentifier('C00999', 'Category', 'pipId'), $childCategory['children'][0]['id']);

        // findChildCategoriesUsedByTleosByParentIdAndType query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
