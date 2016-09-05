<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Broadcast;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use DateTime;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BroadcastsFixture extends AbstractFixture implements DependentFixtureInterface
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

        $version = $this->getReference('v0000001');
        $version2 = $this->getReference('v0000004');
        $version3 = $this->getReference('v0000005');
        $version4 = $this->getReference('v0000006');

        // services
        $service1 = $this->getReference('p00fzl7j');
        $service2 = $this->getReference('p00fzl8v');

        $broadcast1 = $this->buildBroadcast(
            'b0000001',
            $version,
            new DateTime('2016-07-06 06:00:00'),
            new DateTime('2016-07-06 09:25:00'),
            $service1
        );

        $broadcast2 = $this->buildBroadcast(
            'b0000002',
            $version2,
            new DateTime('2011-07-05 15:00:00'),
            new DateTime('2011-07-05 16:00:01'),
            $service1
        );

        $broadcast3 = $this->buildBroadcast(
            'b0000003',
            $version3,
            new DateTime('2011-07-05 15:00:00'),
            new DateTime('2011-07-05 15:25:00'),
            $service2
        );

        $broadcast4 = $this->buildBroadcast(
            'b0000004',
            $version4,
            new DateTime('2011-07-05 15:00:00'),
            new DateTime('2011-07-05 15:25:00'),
            $service2,
            true
        );

        $broadcast5 = $this->buildBroadcast(
            'b0000005',
            $version4,
            new DateTime('2011-07-05 15:00:00'),
            new DateTime('2011-07-05 15:25:00'),
            $service1,
            true
        );

        $manager->flush();
    }

    private function buildBroadcast($pid, $version, $start, $end, $service, $isWebcast = false)
    {
        $entity = new Broadcast($pid, $version, $start, $end);
        $entity->setService($service);
        $entity->setIsWebcast($isWebcast);
        $this->manager->persist($entity);
        $this->addReference($pid, $entity);
        return $entity;
    }
}
