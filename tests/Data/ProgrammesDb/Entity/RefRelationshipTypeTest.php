<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationshipType;
use PHPUnit_Framework_TestCase;

class RefRelationshipTypeTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = new RefRelationshipType();

        $this->assertEquals(null, $entity->getId());
        $this->assertEquals(null, $entity->getName());
        $this->assertEquals(null, $entity->getPid());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new RefRelationshipType();

        $entity->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Name', 'default'],
            ['Pid', 'b006q20x'],
        ];
    }
}
