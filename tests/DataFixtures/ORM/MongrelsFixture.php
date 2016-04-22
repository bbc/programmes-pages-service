<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Clip;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Series;

class MongrelsFixture extends AbstractFixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $brand = $this->buildBrand('b010t19z', 'Mongrels');

        $series1 = $this->buildSeries('b00swyx1', 'Series 1', 1, $brand);
        $series2 = $this->buildSeries('b010t150', 'Series 2', 2, $brand);
        $episodeUnderBrand = $this->buildEpisode('b00tf1zy', 'Mongrels Uncovered', 3, $brand);
        // OK strictly speaking this clip doesn't live under the brand
        // but Mongrels doesn't have an actual clip that lives here in the
        // hierarchy. I won't tell anyone if you won't.
        $clipUnderBrand = $this->buildClip('p00h64pq', 'Mongrels Series 2 Trailer', 4, $brand);

        $s1e1 = $this->buildEpisode('b00swgkn', 'Episode 1', 1, $series1);
        $s1e2 = $this->buildEpisode('b00syxx6', 'Episode 2', 2, $series1);
        $s1e3 = $this->buildEpisode('b00t0ycf', 'Episode 3', 3, $series1);

        $s2e1 = $this->buildEpisode('b0175lqm', 'Episode 1', 1, $series2);
        $s2e2 = $this->buildEpisode('b0176rgj', 'Episode 2', 2, $series2);
        $s2e3 = $this->buildEpisode('b0177ffr', 'Episode 3', 3, $series2);

        $s1c1 = $this->buildClip('p00hv9yz', 'Springwatch', 1, $series1);

        $s1e2c1 = $this->buildClip('p008k0l5', "Who's Paul Ross", 1, $s2e2);
        $s1e2c2 = $this->buildClip('p008k0jy', "Why dogs really bark", 2, $s2e2);

        $s1e3c1 = $this->buildClip('p008nhl4', "Guide dog training", 1, $s2e3);

        $manager->flush();
    }

    private function buildBrand($pid, $title)
    {
        $entity = new Brand($pid, $title);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildSeries($pid, $title, $position, $parent = null)
    {
        $entity = new Series($pid, $title);
        $entity->setPosition($position);
        $entity->setParent($parent);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildEpisode($pid, $title, $position, $parent = null)
    {
        $entity = new Episode($pid, $title);
        $entity->setPosition($position);
        $entity->setParent($parent);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildClip($pid, $title, $position, $parent = null)
    {
        $entity = new Clip($pid, $title);
        $entity->setPosition($position);
        $entity->setParent($parent);
        $this->manager->persist($entity);
        return $entity;
    }
}
