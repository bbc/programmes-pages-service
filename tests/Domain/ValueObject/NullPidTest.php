<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullPid;
use PHPUnit\Framework\TestCase;

class NullPidTest extends TestCase
{
    public function testNullPid()
    {
        $pid = new NullPid();
        $this->assertSame('', (string) $pid);
        $this->assertSame('[null]', json_encode([$pid]));
    }
}
