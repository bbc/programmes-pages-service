<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use PHPUnit_Framework_TestCase;

class ProgrammeTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = $this->getMockForAbstractClass(
            Programme::CLASS,
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
        $this->assertEquals(new ArrayCollection(), $entity->getDirectCategories());
        $this->assertEquals(new ArrayCollection(), $entity->getCategories());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = $this->getMockForAbstractClass(
            Programme::CLASS,
            ['pid', 'title']
        );

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        $genre = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Genre');

        return [
            ['PromotionsCount', 1],
            ['Streamable', true],
            ['HasSupportingContent', true],
            ['Position', 1],
            ['DirectCategories', new ArrayCollection([$genre])],
            ['Categories', new ArrayCollection([$genre])],
        ];
    }
}
