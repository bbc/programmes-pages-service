<?php

namespace Tests\BBC\ProgrammesPagesService\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class BroadcastsEmbargoFixture extends BroadcastsFixture implements DependentFixtureInterface
{
    public const NOW_STRING = '2017-01-01 14:00:00';

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

        // v0000002 is a version of an embargoed episode
        $version = $this->getReference('v0000001');
        $versionEmbargoed = $this->getReference('v0000002');

        $service = $this->getReference('p00fzl7j');

        // A: has an embargoed episode in the past
        $this->buildBroadcast(
            'b000000a',
            $versionEmbargoed,
            new DateTime('2017-01-01 12:00:00'),
            new DateTime('2017-01-01 13:00:00'),
            $service,
            'mb_bbc_radio_four'
        );

        // - B: has an embargoed episode in the future
        $this->buildBroadcast(
            'b000000b',
            $versionEmbargoed,
            new DateTime('2017-01-01 13:30:00'), // note that start is earlier than now
            new DateTime('2017-01-01 15:00:00'),
            $service,
            'mb_bbc_radio_four'
        );


        // - C: has an embargoed webcast in the future
        $this->buildBroadcast(
            'b000000c',
            $versionEmbargoed,
            new DateTime('2017-01-02 12:00:00'),
            new DateTime('2017-01-02 13:00:00'),
            null,
            'mb_bbc_radio_two'
        );

        // - D: has a non-embargoed episode in the future
        $this->buildBroadcast(
            'b000000d',
            $version,
            new DateTime('2017-01-02 12:00:00'),
            new DateTime('2017-01-02 13:00:00'),
            $service,
            'mb_bbc_radio_two'
        );

        $manager->flush();
    }
}
