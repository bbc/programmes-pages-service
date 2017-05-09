<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefMediaSet;
use PHPUnit\Framework\TestCase;

class RefMediaSetTest extends TestCase
{
    public function testDefaults()
    {
        $entity = new RefMediaSet('name');

        $this->assertSame(null, $entity->getId());
        $this->assertSame('name', $entity->getName());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new RefMediaSet('name');

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
