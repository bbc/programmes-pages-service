<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Broadcast;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Segment;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\SegmentEvent;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use DateTime;

class SegmentEventsBroadcastsOrderFixture extends AbstractFixture implements DependentFixtureInterface
{
    private $manager;

    public function getDependencies()
    {
        return [__NAMESPACE__ . '\\VersionFixture'];
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        // Segment for testing broadcast hasBroadcast ASC
        $segment = $this->buildSegment('s0000001', 'chapter');

        $version = $this->getReference('v0000001');
        $this->buildBroadcast(
            'b0000001',
            $version,
            new DateTime('2016-07-06 06:00:00'),
            new DateTime('2016-07-06 09:25:00')
        );

        $embargoedVersion = $this->getReference('v0000002');

        $version2 = $this->getReference('v0000004');

        $version3 = $this->getReference('v0000005');
        $this->buildBroadcast(
            'b0000003',
            $version3,
            new DateTime('2011-07-05 15:00:00'),
            new DateTime('2011-07-05 16:00:01')
        );

        $version4 = $this->getReference('v0000006');
        $this->buildBroadcast(
            'b0000004',
            $version4,
            new DateTime('2011-07-05 15:00:00'),
            new DateTime('2011-07-05 15:25:00')
        );

        $version5 = $this->getReference('v0000007');

        $this->buildSegmentEvent('sv000001', $version, $segment);
        $this->buildSegmentEvent('sv000002', $embargoedVersion, $segment);
        $this->buildSegmentEvent('sv000003', $version2, $segment);
        $this->buildSegmentEvent('sv000004', $version3, $segment);
        $this->buildSegmentEvent('sv000005', $version4, $segment);
        $this->buildSegmentEvent('sv000006', $version5, $segment);

        $manager->flush();
    }

    private function buildSegment($pid, $type)
    {
        $entity = new Segment($pid, $type);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildSegmentEvent($pid, $version, $segment)
    {
        $entity = new SegmentEvent($pid, $version, $segment);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildBroadcast($pid, $version, $start, $end)
    {
        $entity = new Broadcast($pid, $version, $start, $end);
        $this->manager->persist($entity);
        return $entity;
    }
}
