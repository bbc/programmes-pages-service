<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RelatedLink;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Clip;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class RelatedLinkTest extends PHPUnit_Framework_TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RelatedLink');
        $this->assertEquals([
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\SynopsesTrait',
            'Gedmo\Timestampable\Traits\TimestampableEntity',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $coreEntity = new Clip('pid', 'title');
        $link = new RelatedLink('pid', 'title', 'uri', 'type', $coreEntity, false);
        $this->assertSame($coreEntity, $link->getRelatedTo());
        $this->assertSame(null, $link->getId());
        $this->assertSame('pid', $link->getPid());
        $this->assertSame('title', $link->getTitle());
        $this->assertSame('uri', $link->getUri());
        $this->assertSame('type', $link->getType());
        $this->assertSame(false, $link->getIsExternal());
        $this->assertSame(null, $link->getPosition());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $coreEntity = new Clip('pid', 'title');
        $link = new RelatedLink('pid', '', '', '', $coreEntity, false);

        $link->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $link->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Pid', 'a-string'],
            ['Title', 'a-string'],
            ['Uri', 'a-string'],
            ['Type', 'a-string'],
            ['Position', 2],
            ['IsExternal', true],
            ['RelatedTo', new Clip('second', 'title')],
        ];
    }
}
