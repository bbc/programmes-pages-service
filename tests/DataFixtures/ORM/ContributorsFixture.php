<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Contributor;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class ContributorsFixture extends AbstractFixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->buildContributor(
            'cntrbtr1',
            'organisation',
            'The Lonely Island',
            'Lonely Island, The',
            '028e1863-cab4-4a3d-9dd9-91c682c91005'
        );

        $this->buildContributor(
            'cntrbtr2',
            'organisation',
            '“Weird Al” Yankovic',
            'Yankovic, “Weird Al”',
            '7746d775-9550-4360-b8d5-c37bd448ce01'
        );

        $this->buildContributor(
            'cntrbtr3',
            'organisation',
            'Peter Capaldi',
            'Capaldi, Peter',
            '5df5318d-4af6-4349-afc2-7391f092e9e2'
        );

        $manager->flush();
    }

    private function buildContributor($pid, $type, $name, $sortName, $musicBrainz = null)
    {
        $entity = new Contributor($pid, $type);
        $entity->setName($name);
        $entity->setSortName($sortName);
        $entity->setMusicBrainzId($musicBrainz);
        $this->manager->persist($entity);
        $this->addReference($pid, $entity);
        return $entity;
    }
}
