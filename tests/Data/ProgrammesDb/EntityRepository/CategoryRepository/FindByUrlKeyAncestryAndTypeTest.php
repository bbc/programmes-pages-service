<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository::<public>
 */
class FindByUrlKeyAncestryAndTypeTest extends AbstractDatabaseTest
{
    public function testGenreFindByUrlKeyAncestryAndTypeSingle()
    {
        $this->loadFixtures(['CategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entity = $repo->findByUrlKeyAncestryAndType(
            'genre',
            ['drama']
        );

        $this->assertEquals(1, $entity['id']);
        $this->assertEquals('Drama', $entity['title']);
        $this->assertEquals('drama', $entity['urlKey']);
        $this->assertEquals('1,', $entity['ancestry']);

        // findByUrlKeyAncestryAndType query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFormatFindByUrlKeyAncestryAndTypeSingle()
    {
        $this->loadFixtures(['CategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entity = $repo->findByUrlKeyAncestryAndType(
            'format',
            ['animation']
        );

        $this->assertEquals(4, $entity['id']);
        $this->assertEquals('Animation', $entity['title']);
        $this->assertEquals('animation', $entity['urlKey']);
        $this->assertEquals('4,', $entity['ancestry']);

        // findByUrlKeyAncestryAndType query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testGenreFindByUrlKeyAncestryAndTypeOneParent()
    {
        $this->loadFixtures(['CategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entity = $repo->findByUrlKeyAncestryAndType(
            'genre',
            ['actionandadventure', 'drama']
        );

        $this->assertEquals(2, $entity['id']);
        $this->assertEquals('Action & Adventure', $entity['title']);
        $this->assertEquals('actionandadventure', $entity['urlKey']);
        $this->assertEquals('1,2,', $entity['ancestry']);
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testGenreFindByUrlKeyAncestryAndTypeTwoParents()
    {
        $this->loadFixtures(['CategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entity = $repo->findByUrlKeyAncestryAndType(
            'genre',
            ['dramaandaction', 'actionandadventure', 'drama']
        );

        $this->assertEquals(3, $entity['id']);
        $this->assertEquals('Niche Drama & Action', $entity['title']);
        $this->assertEquals('dramaandaction', $entity['urlKey']);
        $this->assertEquals('1,2,3,', $entity['ancestry']);

        $this->assertCount(1, $this->getDbQueries());
    }

    public function testGenreFindByUrlKeyAncestryAndTypeNotFound()
    {
        $this->loadFixtures(['CategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entity = $repo->findByUrlKeyAncestryAndType(
            'genre',
            ['notrealsubsub', 'notrealsub', 'notreal']
        );

        $this->assertNull($entity);

        // findByUrlKeyAncestryAndType query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFormatFindByUrlKeyAncestryAndTypeNotFound()
    {
        $this->loadFixtures(['CategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entity = $repo->findByUrlKeyAncestryAndType(
            'format',
            ['notreal']
        );

        $this->assertNull($entity);

        // findByUrlKeyAncestryAndType query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
