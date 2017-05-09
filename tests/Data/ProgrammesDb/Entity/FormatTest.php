<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Format;
use PHPUnit\Framework\TestCase;

class FormatTest extends TestCase
{
    public function testConstructor()
    {
        $format = new Format('P0006', 'Wibble', 'wibble');
        $parentFormat = new Format('P005', 'Bibble', 'bibble');
        $format->setParent($parentFormat);
        $this->assertEquals('P0006', $format->getPipId());
        $this->assertEquals('Wibble', $format->getTitle());
        $this->assertEquals('wibble', $format->getUrlKey());
        // FORMATS STILL DO NOT HAVE PARENTS. GOOD NIGHT.
        $this->assertEquals(null, $format->getParent());
    }
}
