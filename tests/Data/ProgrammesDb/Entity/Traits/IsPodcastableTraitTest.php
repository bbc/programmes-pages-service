<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Enumeration\IsPodcastableEnum;
use PHPUnit_Framework_TestCase;

class IsPodcastableTraitTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = $this->getMockForTrait('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\IsPodcastableTrait');

        $this->assertEquals(IsPodcastableEnum::NO, $entity->getIsPodcastable());
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
            ['IsPodcastable', IsPodcastableEnum::HIGH],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Called setIsPodcastable with an invalid value. Expected one of "high", "low" or "no" but got "garbage"
     */
    public function testUnknownStatusThrowsException()
    {
        $entity = $this->getMockForTrait('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\IsPodcastableTrait');

        $entity->setIsPodcastable('garbage');
    }
}
