<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationship;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationshipType;
use PHPUnit_Framework_TestCase;

class RefRelationshipTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $relationshipType = new RefRelationshipType('pid', 'name');

        $entity = new RefRelationship(
            'pid',
            'subjectId',
            'subjectType',
            'objectId',
            'objectType',
            $relationshipType
        );

        $this->assertSame(null, $entity->getId());
        $this->assertSame('pid', $entity->getPid());
        $this->assertSame('subjectId', $entity->getSubjectId());
        $this->assertSame('subjectType', $entity->getSubjectType());
        $this->assertSame('objectId', $entity->getObjectId());
        $this->assertSame('objectType', $entity->getObjectType());
        $this->assertSame($relationshipType, $entity->getRelationshipType());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new RefRelationship(
            'pid',
            'subjectId',
            'subjectType',
            'objectId',
            'objectType',
            new RefRelationshipType('pid', 'name')
        );

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
            ['RelationshipType', new RefRelationshipType('pid', 'name')],
        ];
    }
}
