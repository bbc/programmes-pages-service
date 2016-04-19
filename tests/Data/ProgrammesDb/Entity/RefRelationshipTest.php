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

        $this->assertSame(null, $entity->getId());
        $this->assertSame(null, $entity->getPid());
        $this->assertSame(null, $entity->getSubjectId());
        $this->assertSame(null, $entity->getSubjectType());
        $this->assertSame(null, $entity->getObjectId());
        $this->assertSame(null, $entity->getObjectType());
        $this->assertSame(null, $entity->getRelationshipType());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new RefRelationship();

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Pid', 'b006q20x'],
            ['SubjectId', 'b006q20y'],
            ['SubjectType', 'image'],
            ['ObjectId', 'b006q20z'],
            ['ObjectType', 'episode'],
            ['RelationshipType', new RefRelationshipType()],
        ];
    }
}
