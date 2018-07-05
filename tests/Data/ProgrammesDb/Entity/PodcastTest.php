<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Podcast;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PodcastTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Podcast::class);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
        ], $reflection->getTraitNames());
    }
}
