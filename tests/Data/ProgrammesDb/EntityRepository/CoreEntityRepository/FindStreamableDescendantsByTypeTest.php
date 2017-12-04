<?php
declare(strict_types = 1);

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class FindStreamableDescendantsByTypeTest extends AbstractDatabaseTest
{
    public function setUp()
    {
        parent::setUp();

        $this->enableEmbargoedFilter();
    }

    public function tearDown()
    {
        $this->disableEmbargoedFilter();
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

    /**
     * @cover ::findStremableDescendantsByType
     */
    public function testProgrammesChildrenGetCorrectClips()
    {
        $this->loadFixtures(['MongrelsFixture']);

        $dbAncestryId = $this->getAncestryFromPersistentIdentifier('b010t19z', 'Brand');
        $clipsUnderProgramme = $this->getEntityManager()
            ->getRepository('ProgrammesPagesService:CoreEntity')
            ->findStreamableDescendantsByType($dbAncestryId, 'Clip', 100, 0);
        $expectedClipsPids = ['p008nhl4', 'p008k0jy', 'p008k0l5', 'p00hv9yz'];

        $this->assertEquals($expectedClipsPids, array_column($clipsUnderProgramme, 'pid'));
        $this->assertNotContains(false, array_column($clipsUnderProgramme, 'streamable'));
        $this->assertNotContains(true, array_column($clipsUnderProgramme, 'isEmbargoed'));
        $this->assertCount(2, $this->getDbQueries());
    }

    public function testOnDemandSortDate()
    {
        $this->loadFixtures(['MongrelsFixture']);

        $dbAncestryId = $this->getAncestryFromPersistentIdentifier('b010t19z', 'Brand');
        $clipsUnderProgramme = $this->getEntityManager()
            ->getRepository('ProgrammesPagesService:CoreEntity')
            ->findStreamableDescendantsByType($dbAncestryId, 'Episode', 100, 0, true);

        $expectedClipsPids = ['b00syxx6', 'b00swgkn'];

        $this->assertEquals($expectedClipsPids, array_column($clipsUnderProgramme, 'pid'));
        $this->assertNotContains(false, array_column($clipsUnderProgramme, 'streamable'));
        $this->assertNotContains(true, array_column($clipsUnderProgramme, 'isEmbargoed'));
        $this->assertCount(2, $this->getDbQueries());
    }
}
