<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntityImage;
use PHPUnit\Framework\TestCase;

class CoreEntityImageTest extends TestCase
{
    public function testDefaults()
    {
        $coreEntity = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity');
        $image = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image');
        $relationship = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationship');

        $entity = new CoreEntityImage(
            $coreEntity,
            $image,
            'type',
            $relationship
        );

        $this->assertSame(null, $entity->getId());
        $this->assertSame($coreEntity, $entity->getCoreEntity());
        $this->assertSame($image, $entity->getImage());
        $this->assertSame('type', $entity->getType());
        $this->assertSame($relationship, $entity->getRelationship());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $coreEntity = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity');
        $image = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image');
        $relationship = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationship');

        $entity = new CoreEntityImage(
            $coreEntity,
            $image,
            'type',
            $relationship
        );

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        $coreEntity = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity');
        $image = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image');
        $relationship = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefRelationship');

        return [
            ['CoreEntity', $coreEntity],
            ['Image', $image],
            ['Type', 'a-string'],
            ['Relationship', $relationship],
        ];
    }
}
