<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullMid;
use PHPUnit\Framework\TestCase;

class NullMidTest extends TestCase
{
    public function testNullMid()
    {
        $mid = new NullMid();
        $this->assertSame('', (string) $mid);
        $this->assertSame('[null]', json_encode([$mid]));
    }
}
