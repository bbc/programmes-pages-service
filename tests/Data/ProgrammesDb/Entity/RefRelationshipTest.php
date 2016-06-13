<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\EntityRepository;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationship;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationshipType;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class RefRelationshipTest extends AbstractDatabaseTest
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
        $mockCoreEntityImage = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntityImage');
        return [
            ['Pid', 'b006q20x'],
            ['SubjectId', 'b006q20y'],
            ['SubjectType', 'image'],
            ['ObjectId', 'b006q20z'],
            ['ObjectType', 'episode'],
            ['RelationshipType', new RefRelationshipType('pid', 'name')],
            ['CoreEntityImage', $mockCoreEntityImage],
        ];
    }

    public function testDeletesCascadeToCoreImageEntities()
    {
        $this->loadFixtures(['EpisodeImagesFixture']);
        /** @var EntityRepository $relationshipRepo */
        $relationshipRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:RefRelationship');
        $coreImageRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntityImage');


        // Ensure RefRelationship and CoreEntityImage are both present
        $rel = $relationshipRepo->findOneBy(['pid' => 'rel123']);
        $this->assertNotNull($rel);

        $coreImage = $coreImageRepo->find(1);
        $this->assertNotNull($coreImage);

        // Remove the relationship
        $this->getEntityManager()->remove($rel);
        $this->getEntityManager()->flush();

        // Ensure the RefRelationship is deleted and the delete cascaded to the CoreEntityImage
        $rel = $relationshipRepo->findOneBy(['pid' => 'rel123']);
        $this->assertNull($rel);

        $coreImage = $relationshipRepo->find(1);
        $this->assertNull($coreImage);

    }
}
