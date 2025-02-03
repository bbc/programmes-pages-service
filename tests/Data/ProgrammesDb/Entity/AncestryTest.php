<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Ancestry;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AncestryTest extends TestCase
{

    public function testDefaults()
    {
        $ancestor = new Ancestry(12345, 12345);

        $this->assertSame(12345, $ancestor->getAncestorId());
        $this->assertSame(12345, $ancestor->getCoreEntityId());
    }
}
