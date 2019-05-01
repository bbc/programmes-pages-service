<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository::<public>
 */
class FindByIdWithAllDescendantsTest extends AbstractDatabaseTest
{
    public function testFindPopulatedChildCategories()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $category = $repo->findByIdWithAllDescendants(
            $dbId = $this->getDbIdFromPersistentIdentifier('C00193', 'Category', 'pipId'),
            'genre'
        );
        $this->assertEquals($this->getDbIdFromPersistentIdentifier('C00193', 'Category', 'pipId'), $category['id']);
        $this->assertCount(1, $category['children']);

        $childCategory = $category['children'][0];
        $this->assertEquals($this->getDbIdFromPersistentIdentifier('C00196', 'Category', 'pipId'), $childCategory['id']);
        $this->assertEquals('sitcoms', $childCategory['urlKey']);

        $this->assertCount(1, $childCategory['children']);
        $this->assertEquals($this->getDbIdFromPersistentIdentifier('C00999', 'Category', 'pipId'), $childCategory['children'][0]['id']);

        // findChildCategoriesUsedByTleosByParentIdAndType query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
