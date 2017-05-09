<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullNid;
use PHPUnit\Framework\TestCase;

class NullNidTest extends TestCase
{
    public function testNullNid()
    {
        $nid = new NullNid();
        $this->assertSame('', (string) $nid);
        $this->assertSame('[null]', json_encode([$nid]));
    }
}
