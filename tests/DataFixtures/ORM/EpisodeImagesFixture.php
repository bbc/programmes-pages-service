<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntityImage;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationship;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationshipType;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Series;

class EpisodeImagesFixture extends AbstractFixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $episode = $this->makeEpisode();
        $image = $this->buildImage();
        $imageFor = $this->buildRelationshipType();
        $relationship = $this->buildRelationship($imageFor);
        $coreEntityImage = $this->buildCoreEntityImage($episode, $image, $relationship);

        $manager->flush();
    }

    private function makeEpisode()
    {
        $brand = $this->buildBrand('b010t19z', 'Mongrels');
        $series = $this->buildSeries('b00swyx1', 'Series 1', 1, $brand);
        $episode = $this->buildEpisode('b00swgkn', 'Episode 1', 1, $series);

        return $episode;
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

    private function buildImage()
    {
        $entity = new Image('img123', 'title');
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildCoreEntityImage($episode, $image, RefRelationship $relationship)
    {
        $coreEntityImage = new CoreEntityImage($episode, $image, 'is_image_for');
        $this->manager->persist($coreEntityImage);

        $relationship->setCoreEntityImage($coreEntityImage);
        $this->manager->persist($relationship);
        return $coreEntityImage;
    }

    private function buildRelationship($relationshipType)
    {
        $entity = new RefRelationship('rel123', 'img123', 'image', 'epi123', 'episode', $relationshipType);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildRelationshipType()
    {
        $entity = new RefRelationshipType('typ123', 'is_image_for');
        $this->manager->persist($entity);
        return $entity;
    }
}
