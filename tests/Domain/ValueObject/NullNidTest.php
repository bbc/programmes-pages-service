<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullNid;
use PHPUnit_Framework_TestCase;

class NullNidTest extends PHPUnit_Framework_TestCase
{
    public function testNullNid()
    {
        $nid = new NullNid();
        $this->assertSame('', (string) $nid);
        $this->assertSame('[null]', json_encode([$nid]));
    }
}
