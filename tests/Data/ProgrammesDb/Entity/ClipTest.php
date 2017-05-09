<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Clip;
use PHPUnit\Framework\TestCase;

class ClipTest extends TestCase
{
    public function testDefaults()
    {
        $entity = new Clip('pid', 'title');

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeItem',
            $entity
        );
    }
}
