<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\PipsBackfill;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class PipsBackfillFixture extends AbstractFixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->buildChangeEvent('b006m86d');
        $this->buildChangeEvent('b006q2x0');
        $this->buildChangeEvent('b0000000');
        $this->buildChangeEvent('b0000001');

        $manager->flush();
    }

    private function buildChangeEvent($entityId)
    {
        $entity = new PipsBackfill();
        $entity->setCreatedTime(new \DateTime());
        $entity->setType('create');
        $entity->setEntityId($entityId);
        $entity->setEntityType('brand');
        $entity->setStatus('success');
        $entity->setEntityUrl('https://api.live.bbc.co.uk/pips/api/v1/brand/pid.' . $entityId);
        $this->addReference($entityId, $entity);
        $this->manager->persist($entity);
        return $entity;
    }
}
