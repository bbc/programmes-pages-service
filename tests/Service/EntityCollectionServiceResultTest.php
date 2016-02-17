<?php

namespace Tests\BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Service\EntityCollectionServiceResult;
use PHPUnit_Framework_TestCase;

class EntityCollectionServiceResultTest extends PHPUnit_Framework_TestCase
{
    public function testResult()
    {
        $result = new EntityCollectionServiceResult([['foo'], ['bar']], 50, 2);

        $this->assertEquals([['foo'], ['bar']], $result->getResult());
        $this->assertEquals(2, $result->getPage());
        $this->assertEquals(50, $result->getLimit());
        $this->assertEquals(true, $result->hasResult());
    }

    public function testEmptyResult()
    {
        $result = new EntityCollectionServiceResult([], 10, 2);

        $this->assertEquals([], $result->getResult());
        $this->assertEquals(false, $result->hasResult());
    }
}
