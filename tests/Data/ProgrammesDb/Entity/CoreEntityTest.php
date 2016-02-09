<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image;
use PHPUnit_Framework_TestCase;

class CoreEntityTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = $this->getMockForAbstractClass(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity'
        );

        $this->assertEquals(null, $entity->getId());
        $this->assertEquals(null, $entity->getPid());
        $this->assertEquals(false, $entity->getIsEmbargoed());
        $this->assertEquals('', $entity->getTitle());
        $this->assertEquals('', $entity->getSearchTitle());
        $this->assertEquals(null, $entity->getParent());
        $this->assertEquals('', $entity->getAncestry());
        $this->assertEquals('', $entity->getShortSynopsis());
        $this->assertEquals('', $entity->getLongestSynopsis());
        $this->assertEquals(null, $entity->getImage());
        $this->assertEquals(0, $entity->getRelatedLinksCount());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = $this->getMockForAbstractClass(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity'
        );

        $entity->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Pid', 'a-string'],
            ['IsEmbargoed', true],
            ['Title', 'a-string'],
            ['SearchTitle', 'a-string'],
            ['Parent', new Brand()],
            //ancestry
            ['ShortSynopsis', 'a-string'],
            ['LongestSynopsis', 'a-string'],
            ['Image', new Image()],
            ['RelatedLinksCount', 1],
        ];
    }
}
