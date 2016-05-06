<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;

class VersionFixture extends AbstractFixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $episode = $this->buildEpisode('p0000001', 'Ep1');
        $embargoedEpisode = $this->buildEpisode('p0000002', 'Ep2', true);

        $this->buildVersion('v0000001', $episode);
        $this->buildVersion('v0000002', $embargoedEpisode);

        $manager->flush();
    }

    private function buildVersion($pid, $parent)
    {
        $entity = new Version($pid, $parent);
        $this->manager->persist($entity);
        $this->addReference($pid, $entity);
        return $entity;
    }

    private function buildEpisode($pid, $title, $embargoed = false)
    {
        $entity = new Episode($pid, $title);
        $entity->setIsEmbargoed($embargoed);
        $this->addReference($pid, $entity);
        $this->manager->persist($entity);
        return $entity;
    }
}
