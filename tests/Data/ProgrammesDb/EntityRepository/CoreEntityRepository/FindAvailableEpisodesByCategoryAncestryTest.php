<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class FindAvailableEpisodesByCategoryAncestryTest extends AbstractDatabaseTest
{
    public function countAvailableEpisodesByAncestryCategoryIds()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');
        $count = $repo->countAvailableEpisodesByAncestryCategoryIds(
            $dbId = $this->getAncestryFromPersistentIdentifier('C00999', 'Category', 'pipId'),
            30,
            0
        );

        // We expect the count to be 1 as set by the fixture
        $this->assertEquals(1, $count);
    }

    public function testByAvailableEpisodesByAncestryCategoryIds()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');
        $entities = $repo->findAvailableEpisodesByCategoryAncestry(
            $dbId = $this->getAncestryFromPersistentIdentifier('C00999', 'Category', 'pipId'),
            null,
            30,
            0
        );
        // 1 result as expected by fixture
        $this->assertEquals(1, count($entities));
        $this->assertEquals('b0175lqm', $entities[0]['pid']);
        $this->assertEquals('episode', $entities[0]['type']);

        // Parent of episode
        $this->assertEquals('b010t150', $entities[0]['parent']['pid']);
        $this->assertEquals('series', $entities[0]['parent']['type']);

        // Query to get the episode and query to resolve the parents
        $this->assertCount(2, $this->getDbQueries());
    }

    public function testByAvailableEpisodesByAncestryCategoryIdsNoMatch()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');
        $entities = $repo->findAvailableEpisodesByCategoryAncestry(
            ['a', 'b', 'c'], // Ancestry Ids won't match by fixture
            null,
            30,
            0
        );

        $this->assertEquals(0, count($entities));
        $this->assertEmpty($entities);

        // As empty will be returned, it will not query the parents
        $this->assertCount(1, $this->getDbQueries());
    }
}
