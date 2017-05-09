<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use PHPUnit\Framework\TestCase;

class NidTest extends TestCase
{
    public function testValidNid()
    {
        $value = 'bbc_1xtra';
        $nid = new Nid($value);
        $this->assertEquals($value, (string) $nid);
        $this->assertEquals('["bbc_1xtra"]', json_encode([$nid]));
    }

    /**
     * @dataProvider invalidNidDataProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Could not create a Nid from string
     */
    public function testInvalidNids($input)
    {
        $nid = new Nid($input);
    }

    public function invalidNidDataProvider()
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
