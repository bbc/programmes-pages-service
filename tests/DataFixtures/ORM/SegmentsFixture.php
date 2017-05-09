<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Segment;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class SegmentsFixture extends AbstractFixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->buildSegment(
            'sgmntms1',
            'music',
            'Song 1'
        );

        $this->buildSegment(
            'sgmntms2',
            'music',
            'Song 2'
        );

        $this->buildSegment(
            'sgmntms3',
            'music',
            'Song 3'
        );

        $this->buildSegment(
            'sgmntms4',
            'music',
            'Song 4'
        );

        $this->buildSegment(
            'sgmntss1',
            'speech',
            'A Speech Segment'
        );

        $manager->flush();
    }

    private function buildSegment($pid, $type, $title)
    {
        $entity = new Segment($pid, $type);
        $entity->setTitle($title);
        $this->manager->persist($entity);
        $this->addReference($pid, $entity);
        return $entity;
    }
}
