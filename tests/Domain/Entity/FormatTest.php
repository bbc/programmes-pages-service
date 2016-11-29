<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Format;
use PHPUnit_Framework_TestCase;

class FormatTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $format = new Format([0], 'id', 'Title', 'url_key');

        $this->assertEquals('id', $format->getId());
        $this->assertEquals('Title', $format->getTitle());
        $this->assertEquals('url_key', $format->getUrlKey());
    }
}
