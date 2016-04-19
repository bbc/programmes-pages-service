<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationshipType;
use PHPUnit_Framework_TestCase;

class RefRelationshipTypeTest extends PHPUnit_Framework_TestCase
{
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
