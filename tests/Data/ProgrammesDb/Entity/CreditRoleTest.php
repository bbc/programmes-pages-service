<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CreditRole;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class CreditRoleTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(CreditRole::CLASS);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $creditRole = new CreditRole('id');
        $this->assertSame(null, $creditRole->getId());
        $this->assertSame('id', $creditRole->getCreditRoleId());
        $this->assertSame(null, $creditRole->getName());
        $this->assertSame('', $creditRole->getDescription());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $creditRole = new CreditRole('id');

        $creditRole->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $creditRole->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['CreditRoleId', 'a-string'],
            ['Name', 'a-string'],
            ['Description', 'a-string'],
        ];
    }
}
