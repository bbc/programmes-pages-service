<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Format;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Genre;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\MasterBrand;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class TleosByCategoryFixture extends AbstractFixture
{
    private $manager;

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            MongrelsFixture::class,
            NetworksFixture::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $brand1 = $this->buildBrand('11111111', 'Brand1');
        $brand2 = $this->buildBrand('22222222', 'Brand2');

        $e12 = $this->buildEpisode('b01777fa', 'Brand1 Ep 1/2', $brand1);
        $e22 = $this->buildEpisode('b01777fb', 'Brand1 Ep 2/2', $brand1);

        $e13 = $this->buildEpisode('b017j555', 'Brand2 Ep 1/3', $brand2);
        $e23 = $this->buildEpisode('b017j556', 'Brand2 Ep 2/3', $brand2);
        $e33 = $this->buildEpisode('b017j557', 'Brand2 Ep 3/3', $brand2);

        // build categories
        $cat1 = $this->buildGenre('C00123', 'Cat.1', 'cat1');
        $cat11 = $this->buildGenre('C00124', 'Cat.1.1', 'cat11', $cat1);
        $cat111 = $this->buildGenre('C00925', 'Cat.1.1.1', 'cat111', $cat11);

        $form1 = $this->buildFormat('PT010', 'Form.1', 'form1');
        $form2 = $this->buildFormat('PT011', 'Form.2', 'form2');
        $format3 = $this->buildFormat('PT012', 'Form.3', 'form3');

        // assign categories to programmes
        $brand1 = $this->getReference('11111111');
        $brand1->setCategories(new ArrayCollection([$cat1, $cat11, $form1]));
        $brand1->setStreamable(true);
        $manager->persist($brand1);

        $brand2 = $this->getReference('22222222');
        $brand2->setCategories(new ArrayCollection([$cat1, $cat11, $cat111, $form2]));
        $brand2->setStreamable(false);
        $manager->persist($brand2);

        $manager->flush();
    }

    private function buildGenre($pidId, $title, $urlKey, $parent = null)
    {
        $entity = new Genre($pidId, $title, $urlKey);
        $entity->setParent($parent);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildFormat($pidId, $title, $urlKey)
    {
        $entity = new Format($pidId, $title, $urlKey);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildMasterBrand($mid, $pid, $name)
    {
        $entity = new MasterBrand($mid, $pid, $name);
        $this->manager->persist($entity);
        $this->addReference($mid, $entity);
        return $entity;
    }

    private function buildBrand($pid, $title, $embargoed = false)
    {
        $entity = new Brand($pid, $title);
        $entity->setIsEmbargoed($embargoed);
        $this->addReference($pid, $entity);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildEpisode($pid, $title, $parent = null, $embargoed = false)
    {
        $entity = new Episode($pid, $title);
        $entity->setParent($parent);
        $entity->setIsEmbargoed($embargoed);
        $this->addReference($pid, $entity);
        $this->manager->persist($entity);
        return $entity;
    }
}
