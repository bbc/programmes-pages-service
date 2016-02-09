<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Availability;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\MediaSet;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Enumeration\AvailabilityStatusEnum;
use PHPUnit_Framework_TestCase;
use DateTime;
use InvalidArgumentException;

class AvailabilityTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = new Availability();

        $this->assertEquals(null, $entity->getId());
        $this->assertEquals(AvailabilityStatusEnum::PENDING, $entity->getStatus());
        $this->assertEquals(null, $entity->getVersion());
        $this->assertEquals(null, $entity->getStart());
        $this->assertEquals(null, $entity->getEnd());
        $this->assertEquals(new ArrayCollection(), $entity->getMediaSets());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new Availability();

        $entity->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        $mediaSets = new ArrayCollection([1]);

        return [
            ['Status', AvailabilityStatusEnum::AVAILABLE],
            ['Version', new Version()],
            ['Start', new DateTime()],
            ['End', new DateTime()],
            ['MediaSets', $mediaSets],
        ];
    }

    public function testAddMediaSet()
    {
        $ms = new MediaSet();

        $availability = new Availability();
        $availability->addMediaSet($ms);

        $this->assertEquals(new ArrayCollection([$ms]), $availability->getMediaSets());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Called setStatus with an invalid value. Expected one of "available", "future" or "pending" but got "garbage"
     */
    public function testUnknownStatusThrowsException()
    {
        $entity = new Availability();

        $entity->setStatus('garbage');
    }
}
