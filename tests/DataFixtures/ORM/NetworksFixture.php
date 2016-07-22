<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Network;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Service;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

class NetworksFixture extends AbstractFixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $service = $this->buildService(
            'bbc_radio_fourfm',
            'p00fzl7j',
            'Radio Four FM',
            'National Radio',
            'audio'
        );

        $this->buildNetwork(
            'bbc_radio_four',
            'BBC Radio Four',
            $service,
            'radio4'
        );

        $this->manager->flush();
    }

    private function buildService(
        $sid,
        $pid,
        $title,
        $type,
        $mediaType
    ) {
        $entity = new Service($sid, $pid, $title, $type, $mediaType);
        $this->manager->persist($entity);
        $this->addReference($pid, $entity);
        return $entity;
    }

    private function buildNetwork(
        $nid,
        $title,
        $defaultService = null,
        $urlKey = null
    ) {
        $entity = new Network($nid, $title, $title);
        $entity->setDefaultService($defaultService);
        $entity->setUrlKey($urlKey);
        $this->manager->persist($entity);
        $this->addReference('network_' . $nid, $entity);
        return $entity;
    }
}
