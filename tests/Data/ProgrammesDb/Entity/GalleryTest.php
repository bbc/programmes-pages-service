<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Gallery;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class GalleryTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = new Gallery();

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Group',
            $entity
        );
    }
}
