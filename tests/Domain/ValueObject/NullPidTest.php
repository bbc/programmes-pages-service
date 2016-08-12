<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\ValueObject\Null\NullPid;
use PHPUnit_Framework_TestCase;

class NullPidTest extends PHPUnit_Framework_TestCase
{
    public function testNullPid()
    {
        $pid = new NullPid();
        $this->assertSame('', (string) $pid);
        $this->assertSame('[null]', json_encode([$pid]));
    }
}
