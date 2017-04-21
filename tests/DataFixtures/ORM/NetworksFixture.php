<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\MasterBrand;
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

        $network1 = $this->buildNetwork(
            'bbc_radio_four',
            'BBC Radio Four',
            $service,
            'radio4',
            'National Radio',
            'radio',
            5
        );

        $service2 = $this->buildService(
            'bbc_radio_two',
            'p00fzl8v',
            'Radio 2',
            'National Radio',
            'audio'
        );

        $network2 = $this->buildNetwork(
            'bbc_radio_two',
            'BBC Radio 2',
            $service2,
            'radio2',
            'National Radio',
            null,
            3
        );

        $service3 = $this->buildService(
            'bbc_one_cambridge',
            'p00fzl6h',
            'BBC One Cambridgeshire',
            'TV',
            'audio_video'
        );

        $network3 = $this->buildNetwork(
            'bbc_one',
            'BBC One',
            $service3,
            'bbcone',
            'TV',
            'tv',
            1
        );

        $service->setNetwork($network1);
        $service2->setNetwork($network2);

        $this->buildMasterBrand('bbc_radio_four', 'p01y7bwp', 'BBC Radio 4', 'radio4', $network1);

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
        $urlKey = null,
        $type = null,
        $medium = null,
        $position = null
    ) {
        $entity = new Network($nid, $title, $title);
        $entity->setDefaultService($defaultService);
        $entity->setUrlKey($urlKey);
        $entity->setType($type);
        $entity->setPosition($position);

        if ($medium) {
            $entity->setMedium($medium);
        }
        $this->manager->persist($entity);
        $this->addReference('network_' . $nid, $entity);
        return $entity;
    }

    private function buildMasterBrand($mid, $pid, $name, $urlKey, $network = null)
    {
        $entity = new MasterBrand($mid, $pid, $name);
        $entity->setNetwork($network);
        $entity->setUrlKey($urlKey);
        $this->manager->persist($entity);
        $this->addReference('mb_' . $mid, $entity);
        return $entity;
    }
}
