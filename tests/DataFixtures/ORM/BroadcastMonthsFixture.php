<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Broadcast;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use DateTime;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BroadcastMonthsFixture extends AbstractFixture implements DependentFixtureInterface
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
        $embargoedVersion = $this->getReference('v0000002');

        // services
        $service1 = $this->getReference('p00fzl7j');

        $this->buildBroadcast(
            'b0000001',
            $version,
            new DateTime('2016-07-06 06:00:00'),
            new DateTime('2016-07-06 09:25:00'),
            $service1
        );

        $this->buildBroadcast(
            'b0000002',
            $version2,
            new DateTime('2015-06-05 15:00:00'),
            new DateTime('2015-06-05 16:00:01'),
            $service1
        );

        $this->buildBroadcast(
            'b0000003',
            $version2,
            new DateTime('2015-06-04 15:00:00'),
            new DateTime('2015-06-04 16:00:01'),
            $service1
        );

        $this->buildBroadcast(
            'b0000004',
            $version2,
            new DateTime('2015-05-05 15:00:00'),
            new DateTime('2015-05-05 16:00:01'),
            $service1
        );

        // Webcast
        $this->buildBroadcast(
            'w0000001',
            $version,
            new DateTime('2014-06-05 15:00:00'),
            new DateTime('2014-06-05 16:00:01'),
            null
        );

        // Embargoed
        $this->buildBroadcast(
            'b0000009',
            $embargoedVersion,
            new DateTime('2011-07-05 15:00:00'),
            new DateTime('2011-07-05 15:25:00'),
            $service1
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
