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

        $entity = $repo->findPopulatedChildCategoriesByNetworkMedium(1, 'genre', null);

        $this->assertEquals(2, $entity[0]['id']);
        $this->assertEquals('Sitcoms', $entity[0]['title']);
        $this->assertEquals('sitcoms', $entity[0]['urlKey']);
        $this->assertEquals('1,2,', $entity[0]['ancestry']);

        // findChildCategoriesUsedByTleosByParentIdAndType query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindPopulatedChildCategoriesByNetworkMediumWithMedium()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entity = $repo->findPopulatedChildCategoriesByNetworkMedium(1, 'genre', 'radio');

        $this->assertEquals(2, $entity[0]['id']);
        $this->assertEquals('Sitcoms', $entity[0]['title']);
        $this->assertEquals('sitcoms', $entity[0]['urlKey']);
        $this->assertEquals('1,2,', $entity[0]['ancestry']);

        // findChildCategoriesUsedByTleosByParentIdAndType query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
