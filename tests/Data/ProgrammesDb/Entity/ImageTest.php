<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image;
use PHPUnit_Framework_TestCase;

class ImageTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $image = new Image('pid', 'title');

        $this->assertSame(null, $image->getId());
        $this->assertSame('pid', $image->getPid());
        $this->assertSame('title', $image->getTitle());
        $this->assertSame('', $image->getShortSynopsis());
        $this->assertSame('', $image->getMediumSynopsis());
        $this->assertSame('', $image->getLongSynopsis());
        $this->assertSame('standard', $image->getType());
        $this->assertSame('jpg', $image->getExtension());
        $this->assertSame(false, $image->getIsEmbargoed());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $image = new Image('pid', 'title');

        $image->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $image->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Pid', 'a-string'],
            ['Title', 'a-string'],
            ['ShortSynopsis', 'a-string'],
            ['MediumSynopsis', 'a-string'],
            ['LongSynopsis', 'a-string'],
            ['Type', 'standard'],
            ['Extension', 'png'],
            ['IsEmbargoed', true],
        ];
    }
}
