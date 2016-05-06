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

        $this->buildSegmentEvent('se000001', $version, $segment);
        $this->buildSegmentEvent('se000002', $embargoedVersion, $segment);

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
