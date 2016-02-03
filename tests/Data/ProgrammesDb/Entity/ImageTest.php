<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image;
use PHPUnit_Framework_TestCase;

class ImageTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $image = new Image();

        $this->assertEquals(null, $image->getPid());
        $this->assertEquals('', $image->getTitle());
        $this->assertEquals('', $image->getShortSynopsis());
        $this->assertEquals('', $image->getType());
        $this->assertEquals('jpg', $image->getExtension());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $image = new Image();

        $image->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $image->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Pid', 'a-string'],
            ['Title', 'a-string'],
            ['ShortSynopsis', 'a-string'],
            ['Type', 'standard'],
            ['Extension', 'png'],
        ];
    }
}
