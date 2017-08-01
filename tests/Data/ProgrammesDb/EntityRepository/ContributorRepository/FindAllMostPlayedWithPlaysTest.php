<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributorRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributorRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributorRepository::<public>
 */
class FindAllMostPlayedWithPlaysTest extends AbstractDatabaseTest
{
    public function testFindAllMostPlayedWithPlays()
    {
        $this->loadFixtures(['SegmentEventsForArtistsFixture']);
        /** @var ContributorRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Contributor');

        $from = new \DateTimeImmutable('2016-06-30');
        $to = new \DateTimeImmutable('2016-07-06');

        $results = $repo->findAllMostPlayedWithPlays($from, $to, null);

        $this->assertCount(3, $results);

        // This has two plays so should be first
        $this->assertSame('cntrbtr2', $results[0][0]['pid']);
        $this->assertSame('2', $results[0]['contributorPlayCount']);

        // The following each have one play, so the sort order should win
        $this->assertSame('cntrbtr3', $results[1][0]['pid']);
        $this->assertSame('1', $results[1]['contributorPlayCount']);
        $this->assertSame('cntrbtr1', $results[2][0]['pid']);
        $this->assertSame('1', $results[1]['contributorPlayCount']);

        // must have only been one query
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindAllMostPlayedWithPlaysWithService()
    {
        $this->loadFixtures(['SegmentEventsForArtistsFixture']);
        /** @var ContributorRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Contributor');

        $from = new \DateTimeImmutable('2016-06-30');
        $to = new \DateTimeImmutable('2016-07-06');

        $serviceId = $this->getDbIdFromPersistentIdentifier('p00fzl7j', 'Service'); // radio 4

        $results = $repo->findAllMostPlayedWithPlays($from, $to, $serviceId);

        $this->assertCount(2, $results);

        $this->assertSame('cntrbtr1', $results[0][0]['pid']);
        $this->assertSame('1', $results[0]['contributorPlayCount']);

        $this->assertSame('cntrbtr2', $results[1][0]['pid']);
        $this->assertSame('1', $results[1]['contributorPlayCount']);

        // must have only been one query
        $this->assertCount(1, $this->getDbQueries());
    }
}
