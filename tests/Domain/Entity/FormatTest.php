<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Format;
use PHPUnit\Framework\TestCase;

class FormatTest extends TestCase
{
    public function testConstructor()
    {
        $format = new Format([1], 'id', 'Title', 'url_key');

        $this->assertEquals(1, $format->getDbId());
        $this->assertEquals([1], $format->getDbAncestryIds());
        $this->assertEquals('id', $format->getId());
        $this->assertEquals('Title', $format->getTitle());
        $this->assertEquals('url_key', $format->getUrlKey());
    }
}
