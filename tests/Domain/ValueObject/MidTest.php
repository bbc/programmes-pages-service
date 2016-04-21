<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;
use PHPUnit_Framework_TestCase;

class MidTest extends PHPUnit_Framework_TestCase
{
    public function testValidMid()
    {
        $value = 'bbc_1xtra';
        $mid = new Mid($value);
        $this->assertEquals($value, (string) $mid);
        $this->assertEquals('["bbc_1xtra"]', json_encode([$mid]));
    }

    /**
     * @dataProvider invalidMidDataProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Could not create a Mid from string
     */
    public function testInvalidMids($input)
    {
        $mid = new Mid($input);
    }

    public function invalidMidDataProvider()
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
