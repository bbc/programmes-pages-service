<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Broadcast;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Service;
use PHPUnit_Framework_TestCase;

class BroadcastTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $episode = new Episode('$episode_pid', 'episode_title');
        $version = new Version('version_pid', $episode);
        $service = new Service('bbc_radio_1', 'Radio 1', 'radio', 'audio');
        $start = new \DateTime('now');
        $end = new \DateTime('now');

        $entity = new Broadcast('broadcast_pid', $service, $version, $start, $end, 60);

        $this->assertSame(null, $entity->getId());
        $this->assertSame('broadcast_pid', $entity->getPid());
        $this->assertSame($service, $entity->getBroadcaster());
        $this->assertSame($version, $entity->getBroadcastOf());
        $this->assertSame($start, $entity->getStart());
        $this->assertSame($end, $entity->getEnd());
        $this->assertSame(60, $entity->getDuration());
        $this->assertSame(false, $entity->isAudioDescribed());
        $this->assertSame(false, $entity->isBlanked());
        $this->assertSame(false, $entity->isCritical());
        $this->assertSame(false, $entity->isLive());
        $this->assertSame(false, $entity->isRepeat());
    }
}
