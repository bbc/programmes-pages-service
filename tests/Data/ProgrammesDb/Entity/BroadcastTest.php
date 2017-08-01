<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Broadcast;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Service;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class BroadcastTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Broadcast::class);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $episode = new Episode('episode_pid', 'episode_title');
        $version = new Version('version_pid', $episode);
        $start = new \DateTime('now');
        $end = new \DateTime('now');

        $broadcast = new Broadcast('broadcast_pid', $version, $start, $end);

        $this->assertSame(null, $broadcast->getId());
        $this->assertSame('broadcast_pid', $broadcast->getPid());
        $this->assertSame($version, $broadcast->getVersion());
        $this->assertSame($start, $broadcast->getStart());
        $this->assertSame($end, $broadcast->getEnd());
        $this->assertSame(0, $broadcast->getDuration());
        $this->assertSame(null, $broadcast->getService());
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
        $start = new \DateTime('2016-01-01 00:00:00');
        $end = new \DateTime('2016-01-01 00:01:00');
        $broadcast = new Broadcast('broadcast_pid', $version, $start, $end);
        // check the duration logic happened correctly
        $this->assertEquals(60, $broadcast->getDuration());

        $broadcast->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $broadcast->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Version', new Version('version_pid', new Episode('episode_pid', 'episode_title'))],
            ['Service', new Service('bbc_radio_1', 'pid', 'Radio 1', 'radio', 'audio')],
            ['IsBlanked', true],
            ['IsLive', true],
            ['IsRepeat', true],
            ['IsCritical', true],
            ['ProgrammeItem', new Episode('b0000000', 'An Episode')],
            ['IsAudioDescribed', true],
            ['IsWebcast', true],
            ['Pid', 'b0012345'],
            ['Start', new \DateTime('2016-01-01 00:00:00')],
            ['End', new \DateTime('2016-01-01 00:01:00')],
            ['Duration', 60],
        ];
    }
}
