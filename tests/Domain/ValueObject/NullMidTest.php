<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullMid;
use PHPUnit_Framework_TestCase;

class NullMidTest extends PHPUnit_Framework_TestCase
{
    public function testNullMid()
    {
        $mid = new NullMid();
        $this->assertSame('', (string) $mid);
        $this->assertSame('[null]', json_encode([$mid]));
    }
}
