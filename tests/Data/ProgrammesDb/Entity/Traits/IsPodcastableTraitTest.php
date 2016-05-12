<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use PHPUnit_Framework_TestCase;

class IsPodcastableTraitTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = $this->getMockForTrait('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\IsPodcastableTrait');

        $this->assertEquals(false, $entity->getIsPodcastable());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = $this->getMockForTrait('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\IsPodcastableTrait');

        $entity->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['IsPodcastable', true],
        ];
    }
}
