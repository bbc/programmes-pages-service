<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository::<public>
 */
class FindPopulatedChildCategoriesByNetworkMediumTest extends AbstractDatabaseTest
{
    public function testFindPopulatedChildCategoriesByNetworkMediumWithoutMedium()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entity = $repo->findPopulatedChildCategoriesByNetworkMedium(
            $dbId = $this->getDbIdFromPersistentIdentifier('C00193', 'Category', true),
            'genre',
            null
        );

        $this->assertEquals($this->getDbIdFromPersistentIdentifier('C00196', 'Category', true), $entity[0]['id']);
        $this->assertEquals('sitcoms', $entity[0]['urlKey']);

        // findChildCategoriesUsedByTleosByParentIdAndType query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindPopulatedChildCategoriesByNetworkMediumWithMedium()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entity = $repo->findPopulatedChildCategoriesByNetworkMedium(
            $dbId = $this->getDbIdFromPersistentIdentifier('C00193', 'Category', true),
            'genre',
            'radio'
        );

        $this->assertEquals($this->getDbIdFromPersistentIdentifier('C00196', 'Category', true), $entity[0]['id']);
        $this->assertEquals('sitcoms', $entity[0]['urlKey']);

        // findChildCategoriesUsedByTleosByParentIdAndType query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
