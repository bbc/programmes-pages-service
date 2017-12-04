<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use BBC\ProgrammesPagesService\Domain\ApplicationTime;
use DateTimeImmutable;
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
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
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
