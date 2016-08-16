<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Segment;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\SegmentEvent;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SegmentEventFixture extends AbstractFixture implements DependentFixtureInterface
{
    private $manager;

    public function getDependencies()
    {
        return [__NAMESPACE__ . '\\VersionFixture'];
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $segment = $this->buildSegment('s0000001', 'chapter');

        // These come from the VersionFixture
        $version = $this->getReference('v0000001');
        $embargoedVersion = $this->getReference('v0000002');
        $version3 = $this->getReference('v0000003');
        $version4 = $this->getReference('v0000004');
        $version5 = $this->getReference('v0000005');

        $this->buildSegmentEvent('sv000001', $version, $segment);
        $this->buildSegmentEvent('sv000002', $embargoedVersion, $segment);
        $this->buildSegmentEvent('sv000003', $version3, $segment);
        $this->buildSegmentEvent('sv000004', $version4, $segment);
        $this->buildSegmentEvent('sv000005', $version5, $segment);

        // Segment for testing unique versions
        $segment2 = $this->buildSegment('s0000002', 'music');
        $this->buildSegmentEvent('sv000006', $version, $segment2);
        $this->buildSegmentEvent('sv000007', $version, $segment2);
        $this->buildSegmentEvent('sv000008', $version3, $segment2);

        // Segment for testing embargoed filter off
        $segment3 = $this->buildSegment('s0000003', 'chapter');

        $this->buildSegmentEvent('sv00009', $version, $segment3);
        $this->buildSegmentEvent('sv000010', $embargoedVersion, $segment3);
        $this->buildSegmentEvent('sv000011', $version3, $segment3);

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
}
