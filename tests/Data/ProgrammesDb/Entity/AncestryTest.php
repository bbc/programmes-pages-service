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
        $ancestor = new Ancestry('pidAncestor', 'pidCoreEntity');


        $this->assertSame('pidAncestor', $ancestor->getAncestorId());
        $this->assertSame('pidCoreEntity', $ancestor->getCoreEntityId());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $ancestor = new Ancestry('pid', 'title');

        $ancestor->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $ancestor->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['AncestorId', 12577],
            ['CoreEntityId', 12578],
        ];
    }
}
