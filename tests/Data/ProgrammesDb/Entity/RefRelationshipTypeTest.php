<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationshipType;
use ReflectionClass;
use PHPUnit\Framework\TestCase;

class RefRelationshipTypeTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(RefRelationshipType::CLASS);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = new RefRelationshipType('pid', 'name');

        $this->assertSame(null, $entity->getId());
        $this->assertSame('name', $entity->getName());
        $this->assertSame('pid', $entity->getPid());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new RefRelationshipType('pid', 'name');

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Name', 'default'],
            ['Pid', 'b006q20x'],
        ];
    }
}
