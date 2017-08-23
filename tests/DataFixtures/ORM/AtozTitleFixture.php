<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\AtozTitle;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Clip;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Network;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class AtozTitleFixture extends AbstractFixture
{
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $brandTLEO = new Brand('b010t19z', 'Mongrels');
        $masterBrand = new MasterBrand('bbc_one', 'c0000000', 'BBC One');
        $network = new Network('bbc_one', 'BBC One');
        $network->setMedium(NetworkMediumEnum::TV);
        $masterBrand->setNetwork($network);
        $brandTLEO->setMasterBrand($masterBrand);
        $manager->persist($masterBrand);
        $manager->persist($network);
        $manager->persist($brandTLEO);
        $brandTLEOTitle = new AtozTitle($brandTLEO->getTitle(), $brandTLEO);
        $manager->persist($brandTLEOTitle);

        $seriesTLEO = new Series('b0000001', 'The WibbleTron2000');
        $manager->persist($seriesTLEO);
        $seriesTLEOTitle1 = new AtozTitle($seriesTLEO->getTitle(), $seriesTLEO);
        $manager->persist($seriesTLEOTitle1);
        $seriesTLEOTitle2 = new AtozTitle('WibbleTron2000, The', $seriesTLEO);
        $manager->persist($seriesTLEOTitle2);

        $episodeTLEO = new Episode('b0000002', '3000UberWibbleTron3000');
        $episodeTLEO->setStreamable(true);
        $manager->persist($episodeTLEO);
        $episodeTLEOTitle = new AtozTitle($episodeTLEO->getTitle(), $episodeTLEO);
        $this->manager->persist($episodeTLEOTitle);

        $embargoedTLEO = new Brand('b0000004', 'Prince Harry\'s death rattle');
        $embargoedTLEO->setIsEmbargoed(true);
        $manager->persist($embargoedTLEO);
        $embargoedTLEOTitle = new AtozTitle($embargoedTLEO->getTitle(), $embargoedTLEO);
        $manager->persist($embargoedTLEOTitle);

        $clipTLEO = new Clip('b0000003', 'The Best of McWibbleTron');
        $manager->persist($clipTLEO);

        $series1 = new Series('b00swyx1', 'Series 1');
        $series1->setParent($brandTLEO);
        $series1->setPosition(1);
        $manager->persist($series1);

        $s1e1 = new Episode('b00swgkn', 'Episode 1');
        $s1e1->setParent($series1);
        $s1e1->setPosition(1);
        $manager->persist($s1e1);

        $manager->flush();
    }
}
