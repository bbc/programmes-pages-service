<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Programme;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ProgrammeTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Programme::class);
        $this->assertEquals([
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedGalleriesCountMethodsTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = $this->getMockForAbstractClass(
            Programme::class,
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
        $this->assertEquals(0, $entity->getAggregatedGalleriesCount());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = $this->getMockForAbstractClass(
            Programme::class,
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
            ['AggregatedGalleriesCount', 1],
        ];
    }
}
