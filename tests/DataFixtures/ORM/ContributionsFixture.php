<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Contribution;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CreditRole;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Segment;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ContributionsFixture extends AbstractFixture implements DependentFixtureInterface
{
    private $manager;

    public function getDependencies()
    {
        return [
            __NAMESPACE__ . '\\ContributorsFixture',
            __NAMESPACE__ . '\\MongrelsFixture',
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        // build episodes (with full hierarchy)
        $episode1 = $this->getReference('b00swgkn');
        $episode2 = $this->getReference('b00syxx6');

        // build versions
        $version1 = $this->buildVersion('v0000001', $episode1);
        $version2 = $this->buildVersion('v0000002', $episode2);

        // build segments
        $segment1 = $this->buildSegment(
            'sgmntms1',
            'music',
            'Song 1'
        );
        $segment2 = $this->buildSegment(
            'sgmntms2',
            'speech',
            'Speech 2'
        );

        // build contributors
        $contributor1 = $this->getReference('cntrbtr1');
        $contributor2 = $this->getReference('cntrbtr2');

        $role = new CreditRole('PERFORMER');
        $this->manager->persist($role);

        // build contributions
        $contribution1 = $this->buildContribution(
            'cntrbtn1',
            $contributor1,
            $role,
            $version1
        );

        $contribution2 = $this->buildContribution(
            'cntrbtn2',
            $contributor2,
            $role,
            $version1
        );

        $contribution3 = $this->buildContribution(
            'cntrbtn3',
            $contributor1,
            $role,
            $version2
        );

        $contribution4 = $this->buildContribution(
            'cntrbtn4',
            $contributor2,
            $role,
            $version2
        );

        $contribution5 = $this->buildContribution(
            'cntrbtn5',
            $contributor2,
            $role,
            $episode1
        );

        $contribution6 = $this->buildContribution(
            'cntrbtn6',
            $contributor1,
            $role,
            $segment1
        );

        $contribution7 = $this->buildContribution(
            'cntrbtn7',
            $contributor2,
            $role,
            $segment2
        );

        $manager->flush();
    }

    private function buildContribution($pid, $contributor, $role, $segment)
    {
        $entity = new Contribution($pid, $contributor, $role, $segment);
        $this->addReference($pid, $entity);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildVersion($pid, $episode)
    {
        $entity = new Version($pid, $episode);
        $this->addReference($pid, $entity);
        $this->manager->persist($entity);
        return $entity;
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
