<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use PHPUnit\Framework\TestCase;

class PidTest extends TestCase
{
    public function testValidPid()
    {
        $value = 'b010t19z';
        $pid = new Pid($value);
        $this->assertEquals($value, (string) $pid);
        $this->assertEquals('["b010t19z"]', json_encode([$pid]));
    }

    /**
     * @dataProvider invalidPidDataProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Could not create a Pid from string
     */
    public function testInvalidPids($input)
    {
        $pid = new Pid($input);
    }

    public function invalidPidDataProvider()
    {
        return [
            // Too Short
            ['1234567'],
            // Contains uppercase characters
            ['B2345678'],
            // Contains non-alphanumeric characters
            ['b23_45678'],
            ['b23-45678'],
            ['b23|45678'],
            // Contains vowels
            ['abcdefgh'],
        ];
    }
}
