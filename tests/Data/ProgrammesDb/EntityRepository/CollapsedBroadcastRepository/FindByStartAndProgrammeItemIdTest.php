<?php
declare(strict_types = 1);

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class FindByStartAndProgrammeItemIdTest extends AbstractDatabaseTest
{
    public function tearDown(): void
    {
        $this->disableEmbargoedFilter();
    }

    public function testTwoByProgrammeItemIdAndStart(): void
    {
        $this->loadFixtures(['CollapsedBroadcastsWithBroadcastsFixture']);
        $this->enableEmbargoedFilter();


        $broadcastRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');
        $broadcast = $broadcastRepo->findOneBy(['pid' => 'bcb00001']);
        $start = \DateTimeImmutable::createFromMutable($broadcast->getStart());
        $programmeItemId = $broadcast->getVersion()->getProgrammeItem()->getId();
        /** @var CollapsedBroadcastRepository $collapsedBroadcastRepo */
        $collapsedBroadcastRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        $collapsedBroadcasts = $collapsedBroadcastRepo->findByStartAndProgrammeItemId($start, $programmeItemId);
        $collapsedBroadcast = reset($collapsedBroadcasts);


        $this->assertContains($broadcast->getId(), $collapsedBroadcast['broadcastIds']);
        $this->assertCount(2, $collapsedBroadcast['broadcastIds']);
    }

    public function testOneByProgrammeItemIdAndStart(): void
    {
        $this->loadFixtures(['CollapsedBroadcastsWithBroadcastsFixture']);
        $this->enableEmbargoedFilter();


        $broadcastRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');
        $broadcast = $broadcastRepo->findOneBy(['pid' => 'bcb00003']);
        $start = \DateTimeImmutable::createFromMutable($broadcast->getStart());
        $programmeItemId = $broadcast->getVersion()->getProgrammeItem()->getId();
        /** @var CollapsedBroadcastRepository $collapsedBroadcastRepo */
        $collapsedBroadcastRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CollapsedBroadcast');

        $collapsedBroadcasts = $collapsedBroadcastRepo->findByStartAndProgrammeItemId($start, $programmeItemId);
        $collapsedBroadcast = reset($collapsedBroadcasts);
        $this->assertContains($broadcast->getId(), $collapsedBroadcast['broadcastIds']);
        $this->assertCount(1, $collapsedBroadcast['broadcastIds']);
    }
}
