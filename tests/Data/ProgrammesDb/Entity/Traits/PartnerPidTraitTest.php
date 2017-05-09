<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use PHPUnit\Framework\TestCase;

class PartnerPidTraitTest extends TestCase
{
    public function testDefaults()
    {
        $entity = $this->getMockForTrait('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait');

        $this->assertEquals('s0000001', $entity->getPartnerPid());
    }

    public function testSetter()
    {
        $entity = $this->getMockForTrait('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait');

        $entity->setPartnerPid('s0000027');
        $this->assertEquals('s0000027', $entity->getPartnerPid('s0000027'));
    }
}
