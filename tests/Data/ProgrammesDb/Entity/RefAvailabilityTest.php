<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefAvailability;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefMediaSet;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Enumeration\AvailabilityStatusEnum;
use PHPUnit_Framework_TestCase;
use DateTime;
use InvalidArgumentException;

class AvailabilityTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = new RefAvailability();

        $this->assertEquals(null, $entity->getId());
        $this->assertEquals(AvailabilityStatusEnum::PENDING, $entity->getStatus());
        $this->assertEquals(null, $entity->getVersion());
        $this->assertEquals(null, $entity->getScheduledStart());
        $this->assertEquals(null, $entity->getScheduledEnd());
        $this->assertEquals(null, $entity->getActualStart());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new RefAvailability();

        $entity->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Status', AvailabilityStatusEnum::AVAILABLE],
            ['Version', new Version()],
            ['ScheduledStart', new DateTime()],
            ['ScheduledEnd', new DateTime()],
            ['ActualStart', new DateTime()],
            ['MediaSet', new RefMediaSet()],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Called setStatus with an invalid value. Expected one of "available", "future" or "pending" but got "garbage"
     */
    public function testUnknownStatusThrowsException()
    {
        $entity = new RefAvailability();

        $entity->setStatus('garbage');
    }
}
