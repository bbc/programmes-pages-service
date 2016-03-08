<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefMediaSet;
use PHPUnit_Framework_TestCase;

class MediaSetTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = new RefMediaSet();

        $this->assertSame(null, $entity->getId());
        $this->assertSame(null, $entity->getName());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new RefMediaSet();

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Name', 'default'],
        ];
    }
}
