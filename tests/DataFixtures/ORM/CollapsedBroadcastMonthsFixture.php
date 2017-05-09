<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeItem;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CollapsedBroadcastMonthsFixture extends AbstractFixture implements DependentFixtureInterface
{
    private $manager;

    public function getDependencies()
    {
        return [
            __NAMESPACE__ . '\\NetworksFixture',
        ];
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $programme = $this->buildProgrammeItem('p0000001');
        $programme2 = $this->buildProgrammeItem('p0000002');
        $embargoedProgramme = $this->buildProgrammeItem('p0000003', true);

        $this->buildCollapsedBroadcast(
            $programme,
            new DateTime('2016-07-06 06:00:00'),
            new DateTime('2016-07-06 09:25:00'),
            [1]
        );

        $this->buildCollapsedBroadcast(
            $programme,
            new DateTime('2015-06-05 15:00:00'),
            new DateTime('2015-06-05 16:00:01'),
            [1]
        );

        $this->buildCollapsedBroadcast(
            $programme,
            new DateTime('2015-06-04 15:00:00'),
            new DateTime('2015-06-04 16:00:01'),
            [1]
        );

        $this->buildCollapsedBroadcast(
            $programme2,
            new DateTime('2015-05-05 15:00:00'),
            new DateTime('2015-05-05 16:00:01'),
            [1]
        );

        // Webcast
        $this->buildCollapsedBroadcast(
            $programme,
            new DateTime('2014-06-05 15:00:00'),
            new DateTime('2014-06-05 16:00:01'),
            [1],
            true
        );

        // Embargoed
        $this->buildCollapsedBroadcast(
            $embargoedProgramme,
            new DateTime('2011-07-05 15:00:00'),
            new DateTime('2011-07-05 15:25:00'),
            [1]
        );

        $manager->flush();
    }

    private function buildProgrammeItem(string $pid, bool $isEmbargoed = false)
    {
        $entity = new Episode(
            $pid,
            'Wibble'
        );
        $entity->setIsEmbargoed($isEmbargoed);
        $this->manager->persist($entity);
        return $entity;
    }

    private function buildCollapsedBroadcast(
        ProgrammeItem $programmeItem,
        DateTime $start,
        DateTime $end,
        array $serviceIds,
        bool $isWebcastOnly = false
    ) {
        $entity = new CollapsedBroadcast(
            $programmeItem,
            '1',
            implode(',', $serviceIds),
            ($isWebcastOnly ? '1' : '0'),
            $start,
            $end
        );
        $this->manager->persist($entity);
        return $entity;
    }
}
