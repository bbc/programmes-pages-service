<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Broadcast;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Contribution;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CreditRole;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\SegmentEvent;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SegmentEventsForArtistsFixture extends AbstractFixture implements DependentFixtureInterface
{
    private $manager;

    public function getDependencies()
    {
        return [
            __NAMESPACE__ . '\\NetworksFixture',
            __NAMESPACE__ . '\\ContributorsFixture',
            __NAMESPACE__ . '\\SegmentsFixture',
            __NAMESPACE__ . '\\MongrelsFixture',
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        // build contributors
        $contributor1 = $this->getReference('cntrbtr1');
        $contributor2 = $this->getReference('cntrbtr2');
        $contributor3 = $this->getReference('cntrbtr3');

        // build segments
        $segment1 = $this->getReference('sgmntms1');
        $segment2 = $this->getReference('sgmntms2');
        $segment3 = $this->getReference('sgmntms3');
        $segment4 = $this->getReference('sgmntms4');

        $role = new CreditRole('PERFORMER');
        $this->manager->persist($role);

        // build contributions
        $contribution1 = $this->buildContribution(
            'cntrbtn1',
            $contributor1,
            $role,
            $segment1
        );

        $contribution2 = $this->buildContribution(
            'cntrbtn2',
            $contributor2,
            $role,
            $segment2
        );

        $contribution3 = $this->buildContribution(
            'cntrbtn3',
            $contributor2,
            $role,
            $segment3
        );

        $contribution4 = $this->buildContribution(
            'cntrbtn4',
            $contributor3,
            $role,
            $segment4
        );

        // build episodes (with full hierarchy)
        $episode1 = $this->getReference('b00swgkn');
        $episode2 = $this->getReference('b00syxx6');

        // build versions
        $version1 = $this->buildVersion('v0000001', $episode1);
        $version2 = $this->buildVersion('v0000002', $episode2);

        // services
        $service1 = $this->getReference('p00fzl7j');
        $service2 = $this->getReference('p00fzl8v');

        // build a broadcast
        $broadcast1 = $this->buildBroadcast(
            'brdcst01',
            $version1,
            new \DateTime('2016-07-01T12:00:00Z'),
            new \DateTime('2016-07-01T13:00:00Z'),
            $service1
        );

        $broadcast2 = $this->buildBroadcast(
            'brdcst02',
            $version2,
            new \DateTime('2016-07-02T12:00:00Z'),
            new \DateTime('2016-07-02T13:00:00Z'),
            $service2
        );

        $broadcast3 = $this->buildBroadcast(
            'brdcst03',
            $version2,
            new \DateTime('2016-06-02T12:00:00Z'),
            new \DateTime('2016-06-02T13:00:00Z'),
            $service1
        );

        // build the segment events
        $this->buildSegmentEvent('sv000001', $version1, $segment1);
        $this->buildSegmentEvent('sv000002', $version1, $segment2);
        $this->buildSegmentEvent('sv000003', $version2, $segment3);
        $this->buildSegmentEvent('sv000004', $version2, $segment4);

        $manager->flush();
    }

    private function buildBroadcast($pid, $version, $start, $end, $service)
    {
        $entity = new Broadcast($pid, $version, $start, $end);
        $entity->setService($service);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildContribution($pid, $contributor, $role, $segment)
    {
        $entity = new Contribution($pid, $contributor, $role, $segment);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildSegmentEvent($pid, $version, $segment)
    {
        $entity = new SegmentEvent($pid, $version, $segment);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildVersion($pid, $episode)
    {
        $entity = new Version($pid, $episode);
        $this->manager->persist($entity);
        return $entity;
    }
}
