<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Ancestry;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AncestryTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Ancestry::class);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\IsEmbargoedTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\SynopsesTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $ancestor = new Ancestry(12345, 12345);

        $this->assertSame(12345, $ancestor->getAncestorId());
        $this->assertSame(12345, $ancestor->getCoreEntityId());
    }
}
