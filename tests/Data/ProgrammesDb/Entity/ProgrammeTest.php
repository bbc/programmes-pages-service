<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use PHPUnit_Framework_TestCase;

class ProgrammeTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = $this->getMockForAbstractClass(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Programme',
            ['pid', 'title']
        );

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity',
            $entity
        );

        $this->assertEquals(0, $entity->getPromotionsCount());
        $this->assertEquals(false, $entity->getStreamable());
        $this->assertEquals(false, $entity->getHasSupportingContent());
        $this->assertEquals(null, $entity->getPosition());

    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = $this->getMockForAbstractClass(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Programme',
            ['pid', 'title']
        );

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        $genre = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Genre'
        );

        return [
            ['PromotionsCount', 1],
            ['Streamable', true],
            ['HasSupportingContent', true],
            ['Position', 1],
            ['DirectCategories', [$genre]],
            ['Categories', [$genre]],
        ];
    }
}
