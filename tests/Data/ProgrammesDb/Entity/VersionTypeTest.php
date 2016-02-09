<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\VersionType;
use PHPUnit_Framework_TestCase;

class VersionTypeTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = new VersionType();

        $this->assertEquals(null, $entity->getId());
        $this->assertEquals(null, $entity->getType());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new VersionType();

        $entity->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['type', 'default'],
        ];
    }
}
