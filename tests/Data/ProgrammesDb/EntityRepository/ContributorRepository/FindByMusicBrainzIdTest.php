<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributorRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributorRepository::<public>
 */
class FindByMusicBrainzIdTest extends AbstractDatabaseTest
{
    public function testFindByMusicBrainzId()
    {
        $this->loadFixtures(['ContributorsFixture']);
        /** @var ContributorRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Contributor');

        $mid = '028e1863-cab4-4a3d-9dd9-91c682c91005';

        $entity = $repo->findByMusicBrainzId($mid);
        $this->assertInternalType('array', $entity);

        $this->assertEquals($mid, $entity['musicBrainzId']);
        $this->assertEquals('The Lonely Island', $entity['name']);

        // must have only been one query
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByMusicBrainzIdWhenEmptyResult()
    {
        $this->loadFixtures(['ContributorsFixture']);
        /** @var ContributorRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Contributor');

        $mid = '00000000-cab4-4a3d-9dd9-000000000000';

        $entity = $repo->findByMusicBrainzId($mid);
        $this->assertNull($entity);

        // findByMusicBrainzId query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
