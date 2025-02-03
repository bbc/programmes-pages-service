<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Ancestry;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AncestryTest extends TestCase
{

    public function testDefaults()
    {
        $entity = $this->getMockForAbstractClass( 
      12345,      12345);
        
        $this->assertSame(12345, $entity->getAncestorId());
        $this->assertSame(12345, $entity->getCoreEntityId());
    }
}
