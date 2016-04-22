<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use PHPUnit_Framework_TestCase;

class IsEmbargoedTraitTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = $this->getMockForTrait('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\IsEmbargoedTrait');

        $this->assertFalse($entity->getIsEmbargoed());
    }

    public function testSetter()
    {
        $entity = $this->getMockForTrait('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\IsEmbargoedTrait');

        $entity->setIsEmbargoed(true);
        $this->assertTrue($entity->getIsEmbargoed());
    }
}
