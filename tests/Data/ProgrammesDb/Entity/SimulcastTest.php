<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Simulcast;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Service;
use PHPUnit_Framework_TestCase;

class SimulcastTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $version = new Version('version_pid', new Episode('episode_pid', 'episode_title'));
        $start = new \DateTime('now');
        $end = new \DateTime('now');

        $simulcast = new Simulcast($version, $start, $end);

        $this->assertSame(null, $simulcast->getId());
        $this->assertSame($version, $simulcast->getVersion());
        $this->assertSame($start, $simulcast->getStart());
        $this->assertSame($end, $simulcast->getEnd());
        $this->assertSame(0, $simulcast->getDuration());
        $this->assertEmpty($simulcast->getServices());
        $this->assertFalse($simulcast->isRepeat());
        $this->assertFalse($simulcast->isBlanked());
    }

    public function testAddingServices()
    {
        $version = new Version('version_pid', new Episode('episode_pid', 'episode_title'));

        $simulcast = new Simulcast(
            $version,
            new \DateTime('2016-01-01 00:00:00'),
            new \DateTime('2016-01-01 00:00:00')
        );

        $this->assertEmpty($simulcast->getServices());

        $radio1 = new Service('bbc_radio_1', 'Radio 1', 'radio', 'audio');
        $radio2 = new Service('bbc_radio_2', 'Radio 2', 'radio', 'audio');

        $simulcast->addService($radio1);
        $services = $simulcast->getServices();
        $this->assertCount(1, $services);
        $this->assertSame($radio1, $services[0]);

        $simulcast->addService($radio2);
        $services = $simulcast->getServices();
        $this->assertCount(2, $services);
        $this->assertSame($radio1, $services[0]);
        $this->assertSame($radio2, $services[1]);
    }
}
