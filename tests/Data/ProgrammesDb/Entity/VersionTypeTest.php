<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\VersionType;
use PHPUnit_Framework_TestCase;

class VersionTypeTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = new VersionType('type', 'name');

        $this->assertSame(null, $entity->getId());
        $this->assertSame('type', $entity->getType());
        $this->assertSame('name', $entity->getName());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new VersionType('type', 'name');

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['type', 'newType'],
            ['name', 'newName'],
        ];
    }
}
