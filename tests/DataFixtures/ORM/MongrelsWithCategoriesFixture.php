<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Genre;

class MongrelsWithCategoriesFixture extends AbstractFixture implements DependentFixtureInterface
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $category1 = $this->buildGenre('C00193', 'Comedy', 'comedy');
        $category2 = $this->buildGenre('C00196', 'Sitcoms', 'sitcoms', $category1);
        $category3 = $this->buildGenre('C00999', 'Puppety Sitcoms', 'puppetysitcoms', $category2);

        $brand = $this->getReference('b010t19z');
        $brand->setCategories(new ArrayCollection([$category2, $category3]));
        $manager->persist($brand);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [MongrelsFixture::CLASS];
    }

    private function buildGenre($pidId, $title, $urlKey, $parent = null)
    {
        $entity = new Genre($pidId, $title, $urlKey);
        $entity->setParent($parent);
        $this->manager->persist($entity);
        return $entity;
    }
}
