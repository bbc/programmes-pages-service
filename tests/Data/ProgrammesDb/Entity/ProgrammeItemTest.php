<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use PHPUnit_Framework_TestCase;
use DateTime;

class ProgrammeItemTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = $this->getMockForAbstractClass(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeItem'
        );

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Programme',
            $entity
        );

        $this->assertEquals(MediaTypeEnum::UNKNOWN, $entity->getMediaType());
        $this->assertEquals(null, $entity->getStreamableVersion());
        $this->assertEquals(null, $entity->getStreamableFrom());
        $this->assertEquals(null, $entity->getStreamableUntil());
        $this->assertEquals(null, $entity->getDuration());
        $this->assertEquals(null, $entity->getReleaseDate());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = $this->getMockForAbstractClass(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeItem'
        );

        $entity->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['MediaType', MediaTypeEnum::AUDIO],
            ['MediaType', MediaTypeEnum::VIDEO],
            ['MediaType', MediaTypeEnum::UNKNOWN],
            ['StreamableVersion', new Version()],
            ['StreamableFrom', new DateTime()],
            ['StreamableUntil', new DateTime()],
            ['ReleaseDate', new PartialDate('2016')],
            ['Duration', 1],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Called setMediaType with an invalid value. Expected one of "audio", "video" or "" but got "garbage"
     */
    public function testUnknownMediaTypeThrowsException()
    {
        $entity = $this->getMockForAbstractClass(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeItem'
        );

        $entity->setMediaType('garbage');
    }
}
