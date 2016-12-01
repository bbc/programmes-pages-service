<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Genre;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Format;

class CategoriesFixture extends AbstractFixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $genre1 = $this->buildGenre('C00017', 'Drama', 'drama');
        $genre2 = $this->buildGenre('C00018', 'Action & Adventure', 'actionandadventure', $genre1);
        $genre3 = $this->buildGenre('C00019', 'Niche Drama & Action', 'dramaandaction', $genre2);

        $format = $this->buildFormat('PT001', 'Animation', 'animation');

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
