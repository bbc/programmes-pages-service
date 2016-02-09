<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Clip;
use PHPUnit_Framework_TestCase;

class ClipTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = new Clip();

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeItem',
            $entity
        );
    }
}
