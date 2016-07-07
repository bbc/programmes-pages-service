<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Clip;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Series;

class SiblingsFixture extends AbstractFixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $brand = $this->buildBrand('b010t19z', 'Mongrels');

        $series1 = $this->buildSeries('b00swyx1', 'Series 1', 1, $brand);
        $series2 = $this->buildSeries('b00swyx2', 'Series 2', 2, $brand);
        $series3 = $this->buildSeries('b00swyx3', 'Series 3', 3, $brand);

        $s1e1 = $this->buildEpisode('b00swgkn', 'Episode 1', 1, new PartialDate(2014), $series1, new \DateTime('2014-01-01'));
        $s1e2 = $this->buildEpisode('b00syxx6', 'Episode 2', 2, new PartialDate(2015), $series1, new \DateTime('2015-01-01'));
        $s1e3 = $this->buildEpisode('b00syxx7', 'Episode 3', 3, new PartialDate(2016), $series1, new \DateTime('2016-01-01'));

        $s1c1 = $this->buildClip('p00hv9yz', 'Springwatch', 1, new PartialDate(2014), $series1);
        $s1c2 = $this->buildClip('p008k0l5', "Who's Paul Ross", 2, new PartialDate(2015), $series1);

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

    private function buildEpisode($pid, $title, $position, $releaseDate, $parent = null, $firstBroadcastDate = null)
    {
        $entity = new Episode($pid, $title);
        $entity->setPosition($position);
        $entity->setParent($parent);
        $entity->setReleaseDate($releaseDate);
        $entity->setFirstBroadcastDate($firstBroadcastDate);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildClip($pid, $title, $position, $releaseDate, $parent = null)
    {
        $entity = new Clip($pid, $title);
        $entity->setPosition($position);
        $entity->setParent($parent);
        $entity->setReleaseDate($releaseDate);
        $this->manager->persist($entity);
        return $entity;
    }
}
