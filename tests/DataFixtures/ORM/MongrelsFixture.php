<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Series;

class MongrelsFixture extends AbstractFixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $brand = $this->buildBrand('b010t19z', 'Mongrels');

        $series1 = $this->buildSeries('b00swyx1', 'Series 1', $brand);
        $series2 = $this->buildSeries('b010t150', 'Series 2', $brand);
        $episodeUnderBrand = $this->buildEpisode('b00tf1zy', 'Mongrels Uncovered', $brand);

        $s1e1 = $this->buildEpisode('b00swgkn', 'Episode 1', $series1);
        $s1e2 = $this->buildEpisode('b00syxx6', 'Episode 2', $series1);
        $s1e3 = $this->buildEpisode('b00t0ycf', 'Episode 3', $series1);

        $s2e1 = $this->buildEpisode('b0175lqm', 'Episode 1', $series2);
        $s2e2 = $this->buildEpisode('b0176rgj', 'Episode 2', $series2);
        $s2e3 = $this->buildEpisode('b0177ffr', 'Episode 3', $series2);

        $manager->flush();
    }

    private function buildBrand($pid, $title)
    {
        $entity = new Brand();
        $entity->setPid($pid);
        $entity->setTitle($title);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildSeries($pid, $title, $parent = null)
    {
        $entity = new Series();
        $entity->setPid($pid);
        $entity->setTitle($title);
        $entity->setParent($parent);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildEpisode($pid, $title, $parent = null)
    {
        $entity = new Episode();
        $entity->setPid($pid);
        $entity->setTitle($title);
        $entity->setParent($parent);
        $this->manager->persist($entity);
        return $entity;
    }
}
