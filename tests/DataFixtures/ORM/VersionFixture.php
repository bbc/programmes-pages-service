<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\VersionType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class VersionFixture extends AbstractFixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $originalType = $this->buildVersionType('Original', 'Original');
        $otherType = $this->buildVersionType('Other', 'Other');

        $episode = $this->buildEpisode('p0000001', 'Ep1');
        $embargoedEpisode = $this->buildEpisode('p0000002', 'Ep2', true);
        $episode3 = $this->buildEpisode('p0000003', 'Ep3');

        $this->buildVersion('v0000001', $episode, [$originalType, $otherType]);
        $this->buildVersion('v0000002', $embargoedEpisode, [$originalType, $otherType]);
        $this->buildVersion('v0000003', $episode, [$originalType, $otherType]);
        $this->buildVersion('v0000004', $episode);
        $this->buildVersion('v0000005', $episode3);

        $manager->flush();
    }

    private function buildVersion($pid, $parent, array $types = [])
    {
        $entity = new Version($pid, $parent);
        if (!empty($types)) {
            $entity->setVersionTypes(new ArrayCollection($types));
        }

        $this->manager->persist($entity);
        $this->addReference($pid, $entity);
        return $entity;
    }

    private function buildVersionType($type, $title)
    {
        $entity = new VersionType($type, $title);
        $this->manager->persist($entity);
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
