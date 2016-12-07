<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository::<public>
 */
class FindChildCategoriesUsedByTleosByParentIdAndTypeTest extends AbstractDatabaseTest
{
    public function testFindChildCategoriesUsedByTleosByParentIdAndType()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entity = $repo->findChildCategoriesUsedByTleosByParentIdAndType(1, 'genre');

        $this->assertEquals(2, $entity[0]['id']);
        $this->assertEquals('Sitcoms', $entity[0]['title']);
        $this->assertEquals('sitcoms', $entity[0]['urlKey']);
        $this->assertEquals('1,2,', $entity[0]['ancestry']);

        // findChildCategoriesUsedByTleosByParentIdAndType query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindChildCategoriesUsedByTleosByParentIdAndTypeAndMedium()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entity = $repo->findChildCategoriesUsedByTleosByParentIdAndType(1, 'genre', 'radio');

        $this->assertEquals(2, $entity[0]['id']);
        $this->assertEquals('Sitcoms', $entity[0]['title']);
        $this->assertEquals('sitcoms', $entity[0]['urlKey']);
        $this->assertEquals('1,2,', $entity[0]['ancestry']);

        // findChildCategoriesUsedByTleosByParentIdAndType query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
