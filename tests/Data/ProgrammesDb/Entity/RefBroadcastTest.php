<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefBroadcast;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Service;
use PHPUnit_Framework_TestCase;

class RefBroadcastTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $episode = new Episode('episode_pid', 'episode_title');
        $version = new Version('version_pid', $episode);
        $service = new Service('bbc_radio_1', 'Radio 1', 'radio', 'audio');
        $start = new \DateTime('now');
        $end = new \DateTime('now');

        $broadcast = new RefBroadcast('broadcast_pid', $service, $version, $start, $end);

        $this->assertSame(null, $broadcast->getId());
        $this->assertSame('broadcast_pid', $broadcast->getPid());
        $this->assertSame($service, $broadcast->getService());
        $this->assertSame($version, $broadcast->getVersion());
        $this->assertSame($start, $broadcast->getStart());
        $this->assertSame($end, $broadcast->getEnd());
        $this->assertSame(0, $broadcast->getDuration());
        $this->assertSame(false, $broadcast->getIsAudioDescribed());
        $this->assertSame(false, $broadcast->getIsBlanked());
        $this->assertSame(false, $broadcast->getIsCritical());
        $this->assertSame(false, $broadcast->getIsLive());
        $this->assertSame(false, $broadcast->getIsRepeat());
        $this->assertSame(false, $broadcast->getIsWebcast());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $episode = new Episode('episode_pid', 'episode_title');
        $version = new Version('version_pid', $episode);
        $service = new Service('bbc_radio_1', 'Radio 1', 'radio', 'audio');
        $start = new \DateTime('now');
        $end = new \DateTime('now');
        $broadcast = new RefBroadcast('broadcast_pid', $service, $version, $start, $end);

        $broadcast->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $broadcast->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Version', new Version('version_pid', new Episode('episode_pid', 'episode_title'))],
            ['Service', new Service('bbc_radio_1', 'Radio 1', 'radio', 'audio')],
            ['IsBlanked', true],
            ['IsRepeat', true],
            ['IsRepeat', true],
            ['IsCritical', true],
            ['IsAudioDescribed', true],
            ['IsWebcast', true],
            ['Start', new \DateTime('2016-01-01 00:00:00')],
            ['End', new \DateTime('2016-01-01 00:01:00')],
            ['Duration', 60],
        ];
    }
}
