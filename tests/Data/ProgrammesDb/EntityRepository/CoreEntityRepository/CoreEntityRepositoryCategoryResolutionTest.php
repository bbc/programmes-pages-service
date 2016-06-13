<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<!public>
 */
class CoreEntityRepositoryCategoryResolutionTest extends AbstractDatabaseTest
{
    public function testNoAdditionalQueriesWhenNoCategoriesAreFound()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');

        $entity = $repo->findByPidFull('b010t19z');

        $this->assertSame([], $entity['categories']);

        // Ensure only original query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testMultipleEntityRequest()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');

        $entity = $repo->findByPidFull('b010t19z');

        $this->assertCount(2, $entity['categories']);
        $this->assertEquals('C00196', $entity['categories'][0]['pipId']);
        $this->assertEquals('C00999', $entity['categories'][1]['pipId']);

        // Assert hierarchy for sub items
        $this->assertEquals('C00193', $entity['categories'][0]['parent']['pipId']);


        $this->assertEquals('C00196', $entity['categories'][1]['parent']['pipId']);
        $this->assertEquals('C00193', $entity['categories'][1]['parent']['parent']['pipId']);

        $this->assertArrayNotHasKey('parent', $entity['categories'][0]['parent']);
        $this->assertArrayNotHasKey('parent', $entity['categories'][1]['parent']['parent']);

        // Ensure two queries - the original request and one for all categories
        $this->assertCount(2, $this->getDbQueries());
    }
}
