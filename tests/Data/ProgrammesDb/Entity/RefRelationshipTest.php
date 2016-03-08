<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationship;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationshipType;
use PHPUnit_Framework_TestCase;

class RefRelationshipTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = new RefRelationship();

        $this->assertEquals(null, $entity->getId());
        $this->assertEquals(null, $entity->getPid());
        $this->assertEquals(null, $entity->getSubjectPid());
        $this->assertEquals(null, $entity->getObjectPid());
        $this->assertEquals(null, $entity->getRelationshipType());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new RefRelationship();

        $entity->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Pid', 'b006q20x'],
            ['SubjectPid', 'b006q20y'],
            ['ObjectPid', 'b006q20z'],
            ['RelationshipType', new RefRelationshipType()],
        ];
    }
}
