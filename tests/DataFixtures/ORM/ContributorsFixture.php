<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Contributor;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

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
            '028e1863-cab4-4a3d-9dd9-91c682c91005'
        );

        $this->buildContributor(
            'cntrbtr2',
            'organisation',
            '“Weird Al” Yankovic',
            '7746d775-9550-4360-b8d5-c37bd448ce01'
        );

        $manager->flush();
    }

    private function buildContributor($pid, $type, $name, $musicBrainz = null)
    {
        $entity = new Contributor($pid, $type);
        $entity->setName($name);
        $entity->setMusicBrainzId($musicBrainz);
        $this->manager->persist($entity);
        $this->addReference($pid, $entity);
        return $entity;
    }
}
