<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Format;
use PHPUnit_Framework_TestCase;

class FormatTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $format = new Format('Title', 'url_key');

        $this->assertEquals('Title', $format->getTitle());
        $this->assertEquals('url_key', $format->getUrlKey());
    }
}
