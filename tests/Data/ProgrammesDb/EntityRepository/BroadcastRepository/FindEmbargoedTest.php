<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use Tests\BBC\ProgrammesPagesService\DataFixtures\ORM\BroadcastsEmbargoFixture;

/**
 * @covers \BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository::<public>
 */
class FindEmbargoedTest extends AbstractDatabaseTest
{
    public function tearDown()
    {
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

    public function testFindEmbargoedBroadcastsAfter()
    {
        // I need a fixture which:
        // - A: has an embargoed episode in the past
        // - B: has an embargoed episode in the future
        // - C: has an embargoed webcast in the future
        // - D: has a non-embargoed episode in the future
        // And therefore, only B is expected to be returned in the list

        $this->loadFixtures(['BroadcastsEmbargoFixture']);
        /** @var BroadcastRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        // The fixture knows when NOW is, so pull it out of there
        $now = new \DateTimeImmutable(BroadcastsEmbargoFixture::NOW_STRING);

        $broadcasts = $repo->findEmbargoedBroadcastsAfter($now);

        $this->assertCount(1, $broadcasts);
        $this->assertSame('b000000b', $broadcasts[0]['pid']);
    }
}
