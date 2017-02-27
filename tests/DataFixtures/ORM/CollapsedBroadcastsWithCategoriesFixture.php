<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Format;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Genre;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DateTime;

class CollapsedBroadcastsWithCategoriesFixture extends AbstractFixture implements DependentFixtureInterface
{
    private $manager;

    public function getDependencies()
    {
        return [
            __NAMESPACE__ . '\\VersionFixture',
            __NAMESPACE__ . '\\NetworksFixture',
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
        $ep2->setCategories(new ArrayCollection([$category2]));

        $ep3 = $this->getReference('p0000004');
        $ep3->setCategories(new ArrayCollection([$format]));

        //Embargoed
        $ep4 = $this->getReference('p0000002');
        $ep4->setCategories(new ArrayCollection([$category1, $category2, $category3]));

        $ep5 = $this->getReference('p0000006');
        $ep5->setCategories(new ArrayCollection([$category1]));

        $brand = $this->getReference('b0000022');

        // radio, c0000001, p00000001
        $cb1 = $this->buildCollapsedBroadcast(
            $ep1,
            '1,2',
            '3,4',
            new DateTime('2017-01-05 09:30:00'),
            new DateTime('2017-01-05 10:30:00'),
            '0,0'
        );
        $cb1->setTleo($ep1);

        // radio, c0000001, p00000001
        $cb6 = $this->buildCollapsedBroadcast(
            $ep1,
            '5,6',
            '7,8',
            new DateTime('2017-01-04 09:30:00'),
            new DateTime('2017-01-04 10:30:00'),
            '0,1'
        );
        $cb6->setTleo($ep1);

        // c0000001, p00000006
        $cb2 = $this->buildCollapsedBroadcast(
            $ep5,
            '9,10',
            '11,12',
            new DateTime('2017-01-06 09:30:00'),
            new DateTime('2017-01-06 10:30:00'),
            '0,1'
        );
        $cb2->setTleo($ep5);

        // c0000001,c0000002
        $cb3 = $this->buildCollapsedBroadcast(
            $ep2,
            '13,14',
            '15,16',
            new DateTime('2017-02-06 09:30:00'),
            new DateTime('2017-02-06 10:30:00'),
            '0,0'
        );
        $cb3->setTleo($brand);

        // null, c0000001,c0000002,c0000003, p00000002, embargoed
        $cb5 = $this->buildCollapsedBroadcast(
            $ep4,
            '21,22',
            '23,24',
            new DateTime('2017-02-07 09:30:00'),
            new DateTime('2017-02-07 10:30:00'),
            '0,0'
        );
        $cb5->setTleo($ep4);

        // c0000001,c0000002
        $cb3 = $this->buildCollapsedBroadcast(
            $ep2,
            '25,26',
            '27,28',
            new DateTime('2017-02-06 09:31:00'),
            new DateTime('2017-02-06 10:30:00'),
            '1,1',
            true
        );
        $cb3->setTleo($brand);

        $this->manager->flush();
    }

    private function buildCollapsedBroadcast(
        $programmeItem,
        string $broadcastIds,
        string $serviceIds,
        DateTime $start,
        DateTime $end,
        string $areWebcasts,
        bool $isWebcastOnly = false
    ): CollapsedBroadcast {
        $entity = new CollapsedBroadcast($programmeItem, $broadcastIds, $serviceIds, $areWebcasts, $start, $end, $isWebcastOnly);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildGenre($pidId, $title, $urlKey, $parent = null): Genre
    {
        $entity = new Genre($pidId, $title, $urlKey);
        $entity->setParent($parent);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildFormat($pidId, $title, $urlKey): Format
    {
        $entity = new Format($pidId, $title, $urlKey);
        $this->manager->persist($entity);
        return $entity;
    }
}
