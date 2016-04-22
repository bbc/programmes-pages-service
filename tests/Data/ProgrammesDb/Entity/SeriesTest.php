<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Series;
use PHPUnit_Framework_TestCase;

class SeriesTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = new Series('pid', 'title');

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeContainer',
            $entity
        );
    }
}
