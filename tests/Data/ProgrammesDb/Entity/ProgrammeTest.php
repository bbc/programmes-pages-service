<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use PHPUnit_Framework_TestCase;

class ProgrammeTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = $this->getMockForAbstractClass(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Programme'
        );

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity',
            $entity
        );

        $this->assertEquals(0, $entity->getPromotionsCount());
        $this->assertEquals(false, $entity->getIsStreamable());
        $this->assertEquals(false, $entity->getHasSupportingContent());
        $this->assertEquals(null, $entity->getPosition());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = $this->getMockForAbstractClass(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Programme'
        );

        $entity->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['PromotionsCount', 1],
            ['IsStreamable', true],
            ['HasSupportingContent', true],
            ['Position', 1],
        ];
    }
}
