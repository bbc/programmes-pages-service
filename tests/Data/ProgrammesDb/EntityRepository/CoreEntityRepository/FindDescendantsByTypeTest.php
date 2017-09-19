<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @coversDefaultClass BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository
 */
class FindDescendantsByTypeTest extends AbstractDatabaseTest
{
    public function setUp()
    {
        parent::setUp();

        $this->enableEmbargoedFilter();
    }

    public function tearDown()
    {
        $this->disableEmbargoedFilter();
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
        $expectedClipsPids = ['p00h64pq', 'p00hv9yz', 'p008k0l5', 'p008k0jy', 'p008nhl4'];

        $this->assertEmpty(array_diff($expectedClipsPids, array_column($clipsUnderProgramme, 'pid')));
        $this->assertNotContains(false, array_column($clipsUnderProgramme, 'streamable'));
        $this->assertNotContains(true, array_column($clipsUnderProgramme, 'isEmbargoed'));
        $this->assertCount(2, $this->getDbQueries());
    }

    /**
     * @cover ::findDescendantsByType
     */
    public function testProgrammesChildrenGetCorrectGalleries()
    {
        $this->loadFixtures(['MongrelsFixture']);

        $dbAncestryId = $this->getAncestryFromPersistentIdentifier('b010t19z', 'Brand');
        $galleriesUnderProgramme = $this->getEntityManager()
            ->getRepository('ProgrammesPagesService:CoreEntity')
            ->findDescendantsByType($dbAncestryId, 'Gallery', 100, 0);
        $expectedGalleryPids = ['p008nhl6', 'p008nhl5'];

        $this->assertEmpty(array_diff($expectedGalleryPids, array_column($galleriesUnderProgramme, 'pid')));
        $this->assertCount(2, $this->getDbQueries());
    }
}
