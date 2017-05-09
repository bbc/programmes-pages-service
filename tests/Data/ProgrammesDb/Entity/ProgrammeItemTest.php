<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use PHPUnit\Framework\TestCase;
use DateTime;

class ProgrammeItemTest extends TestCase
{
    public function testDefaults()
    {
        $entity = $this->getMockForAbstractClass(
            ProgrammeItem::CLASS,
            ['pid', 'title']
        );

        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Programme',
            $entity
        );

        $this->assertEquals(MediaTypeEnum::UNKNOWN, $entity->getMediaType());
        $this->assertEquals(null, $entity->getStreamableVersion());
        $this->assertEquals(null, $entity->getDownloadableVersion());
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
            ProgrammeItem::CLASS,
            ['pid', 'title']
        );

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        $entity = $this->getMockForAbstractClass(
            ProgrammeItem::CLASS,
            ['pid', 'title']
        );

        return [
            ['MediaType', MediaTypeEnum::AUDIO],
            ['MediaType', MediaTypeEnum::VIDEO],
            ['MediaType', MediaTypeEnum::UNKNOWN],
            ['StreamableVersion', new Version('pid', $entity)],
            ['DownloadableVersion', new Version('pid', $entity)],
            ['StreamableFrom', new DateTime()],
            ['StreamableUntil', new DateTime()],
            ['ReleaseDate', new PartialDate(2016)],
            ['DownloadableMediaSets', ['audio-nondrm-download', 'audio-nondrm-download-low']],
            ['Duration', 1],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUnknownMediaTypeThrowsException()
    {
        $entity = $this->getMockForAbstractClass(
            ProgrammeItem::CLASS,
            ['pid', 'title']
        );

        $entity->setMediaType('garbage');
    }
}
