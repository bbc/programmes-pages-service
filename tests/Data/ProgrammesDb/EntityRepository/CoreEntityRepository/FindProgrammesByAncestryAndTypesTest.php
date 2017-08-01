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
    public function testProgrammesChildrenGetCorrectClips()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $dbAncestryId = $this->getAncestryFromPersistentIdentifier('b010t19z', 'Brand');

        $clipsUnderProgramme = $this->getEntityManager()
            ->getRepository('ProgrammesPagesService:CoreEntity')
            ->findProgrammesByAncestryAndType($dbAncestryId, 'Clip', 100, 0);

        $this->assertCount(5, $clipsUnderProgramme);

        $dbAncestryIdString = implode(',', $dbAncestryId);
        foreach ($clipsUnderProgramme as $clip) {
            $this->assertRegExp("/^$dbAncestryIdString,/", $clip['ancestry']);
            $this->assertEquals(1, $clip['streamable']);
            $this->assertFalse($clip['isEmbargoed']);
        }

        $this->assertCount(1, $this->getDbQueries());
    }

    public function testProgrammesChildrenGetCorrectEpisodes()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $dbAncestryId = $this->getAncestryFromPersistentIdentifier('b010t19z', 'Brand');

        $episodesUnderProgramme = $this->getEntityManager()
            ->getRepository('ProgrammesPagesService:CoreEntity')
            ->findProgrammesByAncestryAndType($dbAncestryId, 'Episode', 100, 0);

        $this->assertCount(7, $episodesUnderProgramme);

        $dbAncestryIdString = implode(',', $dbAncestryId);

        foreach ($episodesUnderProgramme as $episode) {
            $this->assertRegExp("/^$dbAncestryIdString,/", $episode['ancestry']);
            $this->assertEquals(1, $episode['streamable']);
            $this->assertFalse($episode['isEmbargoed']);
        }

        $this->assertCount(1, $this->getDbQueries());
    }
}
