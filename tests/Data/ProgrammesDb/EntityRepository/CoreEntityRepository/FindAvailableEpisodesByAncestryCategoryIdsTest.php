<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class FindAvailableEpisodesByAncestryCategoryIdsTest extends AbstractDatabaseTest
{
    public function countAvailableEpisodesByAncestryCategoryIds()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');
        $count = $repo->countAvailableEpisodesByAncestryCategoryIds(
            [1, 2, 3],
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
        $entities = $repo->findAvailableEpisodesByAncestryCategoryIds(
            [1, 2, 3],
            30,
            0
        );

        $this->assertEquals(1, count($entities)); // 1 result as expected by fixture
        $this->assertEquals('b0175lqm', $entities[0]['pid']);
        $this->assertEquals('Episode 1', $entities[0]['title']);
        $this->assertEquals('episode', $entities[0]['type']);

        // Parent of episode
        $this->assertEquals('b010t150', $entities[0]['parent']['pid']);
        $this->assertEquals('Series 2', $entities[0]['parent']['title']);
        $this->assertEquals('series', $entities[0]['parent']['type']);

        // Query to get the episode and query to resolve the parents
        $this->assertCount(2, $this->getDbQueries());
    }

    public function testByAvailableEpisodesByAncestryCategoryIdsNoMatch()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:CoreEntity');
        $entities = $repo->findAvailableEpisodesByAncestryCategoryIds(
            [2, 3, 4], // Ancestry Ids won't match by fixture
            30,
            0
        );

        $this->assertEquals(0, count($entities));
        $this->assertEmpty($entities);

        // As empty will be returned, it will not query the parents
        $this->assertCount(1, $this->getDbQueries());
    }
}
