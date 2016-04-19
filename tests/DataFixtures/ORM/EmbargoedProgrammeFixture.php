<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;

class EmbargoedProgrammeFixture extends AbstractFixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $brand = $this->buildBrand('b017j7vs', 'Old Jews Telling Jokes');

        $e1 = $this->buildEpisode('b01777fr', 'Episode 1', $brand);
        $e2 = $this->buildEpisode('b017j5jw', 'Episode 2', $brand);

        // The mythical 3rd episode doesn't exist, but we want to prove that
        // embargoed items don't get returned when querying
        $e3Embargoed = $this->buildEpisode('99999999', 'Episode 3', $brand, true);

        $manager->flush();
    }

    private function buildBrand($pid, $title)
    {
        $entity = new Brand($pid, $title);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildEpisode($pid, $title, $parent = null, $embargoed = false)
    {
        $entity = new Episode($pid, $title);
        $entity->setParent($parent);
        $entity->setIsEmbargoed($embargoed);
        $this->manager->persist($entity);
        return $entity;
    }
}
