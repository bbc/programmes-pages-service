<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Format;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Genre;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class BroadcastsWithCategoriesFixture extends AbstractFixture implements DependentFixtureInterface
{
    private $manager;

    public function getDependencies()
    {
        return [
            __NAMESPACE__ . '\\BroadcastsFixture',
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $category1 = $this->buildGenre('c0000001', 'Music', 'music');
        $category2 = $this->buildGenre('c0000002', 'Jazz And Blues', 'jazzandblues', $category1);
        $category3 = $this->buildGenre('c0000003', 'Blues', 'blues', $category2);
        $format = $this->buildFormat('f0000001', 'Format', 'format');

        $ep1 = $this->getReference('p0000001');
        $ep1->setCategories(new ArrayCollection([$category1]));

        $ep2 = $this->getReference('p0000003');
        $ep2->setCategories(new ArrayCollection([$category1, $category2]));

        $ep3 = $this->getReference('p0000004');
        $ep3->setCategories(new ArrayCollection([$format]));

        //Embargoed
        $ep4 = $this->getReference('p0000002');
        $ep4->setCategories(new ArrayCollection([$category1, $category2, $category3]));

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
}
