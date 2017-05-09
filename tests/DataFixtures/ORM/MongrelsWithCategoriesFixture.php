<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Genre;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class MongrelsWithCategoriesFixture extends AbstractFixture implements DependentFixtureInterface
{
    private $manager;

    public function getDependencies()
    {
        return [MongrelsFixture::CLASS, NetworksFixture::CLASS];
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $category1 = $this->buildGenre('C00193', 'Comedy', 'comedy');
        $category2 = $this->buildGenre('C00196', 'Sitcoms', 'sitcoms', $category1);
        $category3 = $this->buildGenre('C00999', 'Puppety Sitcoms', 'puppetysitcoms', $category2);

        $brand = $this->getReference('b010t19z');
        $brand->setCategories(new ArrayCollection([$category2, $category3]));
        $brand->setMasterBrand($this->getReference('mb_bbc_radio_four'));
        $manager->persist($brand);

        $brand = $this->getReference('b00swgkn');
        $brand->setCategories(new ArrayCollection([$category1, $category2]));
        $manager->persist($brand);

        $s2e1 = $this->getReference('b0175lqm');
        $s2e1->setStreamable(true);
        $s2e1->setCategories(new ArrayCollection([$category3]));
        $manager->persist($s2e1);

        $manager->flush();
    }

    private function buildGenre($pidId, $title, $urlKey, $parent = null)
    {
        $entity = new Genre($pidId, $title, $urlKey);
        $entity->setParent($parent);
        $this->manager->persist($entity);
        return $entity;
    }
}
