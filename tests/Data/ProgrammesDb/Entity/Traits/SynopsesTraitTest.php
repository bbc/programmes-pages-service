<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\SynopsesTrait;
use PHPUnit\Framework\TestCase;

class SynopsesTraitTest extends TestCase
{
    public function testDefaults()
    {
        /** @var SynopsesTrait $entity */
        $entity = $this->getMockForTrait('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\SynopsesTrait');

        $this->assertEquals('', $entity->getShortSynopsis());
        $this->assertEquals('', $entity->getMediumSynopsis());
        $this->assertEquals('', $entity->getLongSynopsis());
    }

    public function testSetters()
    {
        /** @var SynopsesTrait $entity */
        $entity = $this->getMockForTrait('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\SynopsesTrait');

        $entity->setShortSynopsis('short');
        $entity->setMediumSynopsis('medium');
        $entity->setLongSynopsis('long');
        $this->assertEquals('short', $entity->getShortSynopsis());
        $this->assertEquals('medium', $entity->getMediumSynopsis());
        $this->assertEquals('long', $entity->getLongSynopsis());
    }
}
