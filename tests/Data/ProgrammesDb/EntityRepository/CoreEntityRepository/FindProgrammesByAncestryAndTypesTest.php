<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 * @group repository
 * @group programmesAggregation
 */
class FindProgrammesByAncestryAndTypesTest extends AbstractDatabaseTest
{
    public function testFindEpisodeGuideChildrenGetCorrectClips()
    {
        $this->loadFixtures(['MongrelsFixture']);

        $clipsUnderProgramme = $this->getEntityManager()
            ->getRepository('ProgrammesPagesService:CoreEntity')
            ->findProgrammesByAncestryAndType([1, 15], 'Clip', 100, 0);


        $this->assertCount(3, $clipsUnderProgramme);
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindEpisodeGuideChildrenGetCorrectEpisodes()
    {
        $this->loadFixtures(['MongrelsFixture']);

        $episodesUnderProgramme = $this->getEntityManager()
                                    ->getRepository('ProgrammesPagesService:CoreEntity')
                                    ->findProgrammesByAncestryAndType([1, 14], 'Episode', 100, 0);


        $this->assertCount(3, $episodesUnderProgramme);
        $this->assertCount(1, $this->getDbQueries());
    }
}
