<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Gallery;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GalleryTest extends TestCase
{
    public function testDefaults()
    {
        $entity = new Gallery('pid', 'title');

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Group',
            $entity
        );
    }
}
