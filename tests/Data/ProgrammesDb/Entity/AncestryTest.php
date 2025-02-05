<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Ancestry;
use PHPUnit\Framework\TestCase;

class AncestryTest extends TestCase
{

    public function testDefaults()
    {
        $entity = new Ancestry(12345, 12345);
        $this->assertSame(12345, $entity->getAncestorId());
        $this->assertSame(12345, $entity->getCoreEntityId());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new Ancestry(12345, 12345);

        $entity->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['AncestorId', 12345],
            ['CoreEntityId', 12345],
        ];
    }
}
