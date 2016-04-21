<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use PHPUnit_Framework_TestCase;

class SidTest extends PHPUnit_Framework_TestCase
{
    public function testValidSid()
    {
        $value = 'bbc_1xtra';
        $sid = new Sid($value);
        $this->assertEquals($value, (string) $sid);
        $this->assertEquals('["bbc_1xtra"]', json_encode([$sid]));
    }

    /**
     * @dataProvider invalidSidDataProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Could not create a Sid from string
     */
    public function testInvalidSids($input)
    {
        $sid = new Sid($input);
    }

    public function invalidSidDataProvider()
    {
        return [
            // Too Short
            [''],
            // Contains non-alphanumeric characters
            ['b23-45678'],
            ['b23|45678'],
        ];
    }
}
