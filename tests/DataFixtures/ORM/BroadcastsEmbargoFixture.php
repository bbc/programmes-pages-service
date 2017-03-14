<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Broadcast;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use DateTime;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BroadcastsEmbargoFixture extends AbstractFixture implements DependentFixtureInterface
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

        // v0000002 is a version of an embargoed episode
        $version = $this->getReference('v0000001');
        $versionEmbargoed = $this->getReference('v0000002');

        $service = $this->getReference('p00fzl7j');

        // For this fixture: NOW = 2017-01-01 14:00:00

        // A: has an embargoed episode in the past
        $this->buildBroadcast(
            'b000000a',
            $versionEmbargoed,
            new DateTime('2017-01-01 12:00:00'),
            new DateTime('2017-01-01 13:00:00'),
            $service
        );

        // - B: has an embargoed episode in the future
        $this->buildBroadcast(
            'b000000b',
            $versionEmbargoed,
            new DateTime('2017-01-01 13:30:00'), // note that start is earlier than now
            new DateTime('2017-01-01 15:00:00'),
            $service
        );


        // - C: has an embargoed webcast in the future
        $this->buildBroadcast(
            'b000000c',
            $versionEmbargoed,
            new DateTime('2017-01-02 12:00:00'),
            new DateTime('2017-01-02 13:00:00'),
            null
        );

        // - D: has a non-embargoed episode in the future
        $this->buildBroadcast(
            'b000000d',
            $version,
            new DateTime('2017-01-02 12:00:00'),
            new DateTime('2017-01-02 13:00:00'),
            $service
        );

        $manager->flush();
    }

    private function buildBroadcast($pid, $version, $start, $end, $service)
    {
        $entity = new Broadcast($pid, $version, $start, $end);
        $entity->setService($service);
        $entity->setProgrammeItem($version->getProgrammeItem());
        $entity->setIsWebcast(is_null($service));
        $this->manager->persist($entity);
        $this->addReference($pid, $entity);
        return $entity;
    }
}
