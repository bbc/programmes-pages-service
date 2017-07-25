<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Broadcast;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CollapsedBroadcast;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CollapsedBroadcastsWithBroadcastsFixture extends AbstractFixture implements DependentFixtureInterface
{
    private $manager;

    public function getDependencies()
    {
        return [
            __NAMESPACE__ . '\\VersionFixture',
            __NAMESPACE__ . '\\NetworksFixture',
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $broadcast1 = $this->buildBroadcast(
            'bcb00001',
            'v0000001',
            new DateTime('2011-07-05 15:00:00'),
            new DateTime('2011-07-05 16:00:00')
        );
        $broadcast2 = $this->buildBroadcast(
            'bcb00002',
            'v0000003',
            new DateTime('2011-07-05 15:00:00'),
            new DateTime('2011-07-05 16:00:00')
        );
        $broadcast3 = $this->buildBroadcast(
            'bcb00003',
            'v0000005',
            new DateTime('2011-07-05 15:00:00'),
            new DateTime('2011-07-05 16:00:00')
        );
        $this->manager->flush();


        $this->buildCollapsedBroadcast(
            [$broadcast1->getPid(), $broadcast2->getPid()],
            [1, 2]
        );

        $this->buildCollapsedBroadcast(
            [$broadcast3->getPid()],
            [3]
        );

        $manager->flush();
    }

    private function buildBroadcast(
        string $pid,
        string $versionPid,
        DateTime $start,
        DateTime $end
    ) {
        $version = $this->getReference($versionPid);
        $broadcast = new Broadcast($pid, $version, $start, $end);
        $broadcast->setProgrammeItem($version->getProgrammeItem());
        $this->setReference($pid, $broadcast);
        $this->manager->persist($broadcast);
        return $broadcast;
    }

    private function buildCollapsedBroadcast(
        array $broadcastPids,
        array $serviceIds
    ) {
        $broadcastIds = [];
        $broadcasts = [];
        $areWebcasts = [];
        foreach ($broadcastPids as $broadcastPid) {
            $broadcast = $broadcasts[] = $this->getReference($broadcastPid);
            $broadcastIds[] = $broadcast->getId();
            $areWebcasts[] = 0;
        }
        $programmeItem = $broadcasts[0]->getVersion()->getProgrammeItem();
        $entity = new CollapsedBroadcast(
            $programmeItem,
            implode(',', $broadcastIds),
            implode(',', $serviceIds),
            implode(',', $areWebcasts),
            $broadcasts[0]->getStart(),
            $broadcasts[0]->getEnd()
        );
        $this->manager->persist($entity);
        return $entity;
    }
}
