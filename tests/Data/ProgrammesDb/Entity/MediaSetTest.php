<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\MediaSet;
use PHPUnit_Framework_TestCase;

class MediaSetTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = new MediaSet();

        $this->assertEquals(null, $entity->getId());
        $this->assertEquals(null, $entity->getType());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new MediaSet();

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
