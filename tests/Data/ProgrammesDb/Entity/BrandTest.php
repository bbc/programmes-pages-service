<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use PHPUnit\Framework\TestCase;

class BrandTest extends TestCase
{
    public function testDefaults()
    {
        $entity = new Brand('pid', 'title');

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeContainer',
            $entity
        );
    }
}
