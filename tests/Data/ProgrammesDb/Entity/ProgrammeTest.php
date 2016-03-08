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

        $this->assertSame(0, $entity->getPromotionsCount());
        $this->assertSame(false, $entity->getIsStreamable());
        $this->assertSame(false, $entity->getHasSupportingContent());
        $this->assertSame(null, $entity->getReleaseDate());
        $this->assertSame(null, $entity->getPosition());
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
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['PromotionsCount', 1],
            ['IsStreamable', true],
            ['HasSupportingContent', true],
            ['ReleaseDate', new PartialDate('2016')],
            ['Position', 1],
        ];
    }
}
