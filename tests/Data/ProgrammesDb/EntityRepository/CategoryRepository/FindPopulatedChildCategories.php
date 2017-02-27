<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository::<public>
 */
class FindPopulatedChildCategoriesTest extends AbstractDatabaseTest
{
    public function testFindPopulatedChildCategories()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entity = $repo->findPopulatedChildCategories(
            $dbId = $this->getDbIdFromPersistentIdentifier('C00193', 'Category', 'pipId'),
            'genre'
        );

        $this->assertEquals($this->getDbIdFromPersistentIdentifier('C00196', 'Category', 'pipId'), $entity[0]['id']);
        $this->assertEquals('sitcoms', $entity[0]['urlKey']);

        // findChildCategoriesUsedByTleosByParentIdAndType query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
