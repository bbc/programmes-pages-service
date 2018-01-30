<?php
declare(strict_types = 1);

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class CountStreamableDescendantsByTypeTest extends AbstractDatabaseTest
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
     * @cover ::countStremableDescendantsByType
     */
    public function testProgrammesChildrenGetCorrectClips()
    {
        $this->loadFixtures(['MongrelsFixture']);

        $dbAncestryId = $this->getAncestryFromPersistentIdentifier('b010t19z', 'Brand');
        $clipsUnderProgrammeCount = $this->getEntityManager()
            ->getRepository('ProgrammesPagesService:CoreEntity')
            ->countStreamableDescendantsByType($dbAncestryId, 'Clip');

        $this->assertEquals(4, $clipsUnderProgrammeCount);
        $this->assertCount(1, $this->getDbQueries());
    }
}
