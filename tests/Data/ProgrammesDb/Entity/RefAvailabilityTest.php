<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefAvailability;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefMediaSet;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Enumeration\AvailabilityStatusEnum;
use PHPUnit\Framework\TestCase;
use DateTime;
use InvalidArgumentException;

class RefAvailabilityTest extends TestCase
{
    public function testDefaults()
    {
        $programmeItem = new Episode('pid', 'title');
        $version = new Version('pid', $programmeItem);
        $mediaSet = new RefMediaSet('media');
        $scheduledStart = new DateTime();

        $entity = new RefAvailability(
            'type',
            $version,
            $programmeItem,
            $mediaSet,
            $scheduledStart
        );

        $this->assertSame(null, $entity->getId());
        $this->assertSame('type', $entity->getType());
        $this->assertSame($version, $entity->getVersion());
        $this->assertSame($programmeItem, $entity->getProgrammeItem());
        $this->assertSame($mediaSet, $entity->getMediaSet());
        $this->assertSame($scheduledStart, $entity->getScheduledStart());
        $this->assertSame(null, $entity->getScheduledEnd());
        $this->assertSame(null, $entity->getActualStart());
        $this->assertSame(AvailabilityStatusEnum::PENDING, $entity->getStatus());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $episode = new Episode('pid', 'title');

        $entity = new RefAvailability(
            'type',
            new Version('pid', $episode),
            $episode,
            new RefMediaSet('media'),
            new DateTime()
        );

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        $episode = new Episode('pid', 'title');

        return [
            ['Type', 'audio_nondrm_download'],
            ['Version', new Version('pid', $episode)],
            ['ProgrammeItem', $episode],
            ['MediaSet', new RefMediaSet('mediaSet')],
            ['ScheduledStart', new DateTime()],
            ['ScheduledEnd', new DateTime()],
            ['ActualStart', new DateTime()],
            ['Status', AvailabilityStatusEnum::AVAILABLE],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Called setStatus with an invalid value. Expected one of "available", "future" or "pending" but got "garbage"
     */
    public function testUnknownStatusThrowsException()
    {
        $episode = new Episode('pid', 'title');

        $entity = new RefAvailability(
            'type',
            new Version('pid', $episode),
            $episode,
            new RefMediaSet('media'),
            new DateTime()
        );

        $entity->setStatus('garbage');
    }
}
