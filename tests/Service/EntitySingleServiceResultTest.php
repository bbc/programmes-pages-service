<?php

namespace Tests\BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Service\EntitySingleServiceResult;
use PHPUnit_Framework_TestCase;

class EntitySingleServiceResultTest extends PHPUnit_Framework_TestCase
{
    public function testResult()
    {
        $result = new EntitySingleServiceResult(['foo']);

        $this->assertEquals(['foo'], $result->getResult());
        $this->assertEquals(true, $result->hasResult());
    }

    public function testEmptyResult()
    {
        $result = new EntitySingleServiceResult(null);

        $this->assertEquals(null, $result->getResult());
        $this->assertEquals(false, $result->hasResult());
    }
}
