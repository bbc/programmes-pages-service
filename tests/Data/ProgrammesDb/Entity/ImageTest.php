<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ImageTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Image::class);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\IsEmbargoedTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\SynopsesTrait',
        ], $reflection->getTraitNames());
    }

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
        $this->assertSame(null, $image->getHeight());
        $this->assertSame(null, $image->getWidth());
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
            ['Height', 600],
            ['Width', 400],
        ];
    }
}
