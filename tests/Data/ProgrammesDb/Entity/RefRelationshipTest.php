<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationship;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationshipType;
use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RefRelationshipTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(RefRelationship::CLASS);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $relationshipType = new RefRelationshipType('pid123', 'name');

        $entity = new RefRelationship(
            'pid',
            'subjectId',
            'subjectType',
            'objectId',
            'objectType',
            $relationshipType,
            new DateTime('2015-06-13T11:06:03Z')
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
            new RefRelationshipType('pid', 'name'),
            new DateTime('2015-06-13T11:06:03Z')
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
