<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgrammeItem;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CollapsedBroadcastTest extends TestCase
{
    public function testConstructorRequiredArgs()
    {
        $programmeItem = $this->createMock(ProgrammeItem::class);
        $services = [$this->createMock(Service::class)];
        $startAt = new DateTimeImmutable('2015-01-01 06:00:00');
        $endAt = new DateTimeImmutable('2015-01-01 07:00:00');

        $broadcast = new CollapsedBroadcast(
            $programmeItem,
            $services,
            $startAt,
            $endAt,
            1
        );

        $this->assertSame($programmeItem, $broadcast->getProgrammeItem());
        $this->assertSame($services, $broadcast->getServices());
        $this->assertSame($startAt, $broadcast->getStartAt());
        $this->assertSame($endAt, $broadcast->getEndAt());
        $this->assertSame(1, $broadcast->getDuration());
        $this->assertSame(false, $broadcast->isBlanked());
        $this->assertSame(false, $broadcast->isRepeat());

        // Exactly at the start and a moment before - starts are inclusive
        $this->assertTrue($broadcast->isOnAirAt(new DateTimeImmutable('2015-01-01 06:00:00')));
        $this->assertFalse($broadcast->isOnAirAt(new DateTimeImmutable('2015-01-01 05:59:59')));

        // Exactly at the end and a moment after - ends are exclusive
        $this->assertTrue($broadcast->isOnAirAt(new DateTimeImmutable('2015-01-01 06:59:59')));
        $this->assertFalse($broadcast->isOnAirAt(new DateTimeImmutable('2015-01-01 07:00:00')));
    }

    public function testConstructorOptionalArgs()
    {
        $broadcast = new CollapsedBroadcast(
            $this->createMock(ProgrammeItem::class),
            [$this->createMock(Service::class)],
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            1,
            true,
            true
        );

        $this->assertSame(true, $broadcast->isBlanked());
        $this->assertSame(true, $broadcast->isRepeat());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Tried to create a CollapsedBroadcast with invalid Services. Expected a non-empty array of Services but the array was empty
     */
    public function testConstructorInvalidServicesCanNotBeEmpty()
    {
        new CollapsedBroadcast(
            $this->createMock(ProgrammeItem::class),
            [],
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            1
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Tried to create a CollapsedBroadcast with invalid Services. Expected a non-empty array of Services but the array contained an instance of "string"
     */
    public function testConstructorInvalidServicesMustContainServices()
    {
        new CollapsedBroadcast(
            $this->createMock(ProgrammeItem::class),
            ['garbage'],
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            1
        );
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get ProgrammeItem of CollapsedBroadcast as it was not fetched
     */
    public function testRequestingUnfetchedProgrammeItemThrowsException()
    {
        $broadcast = new CollapsedBroadcast(
            $this->createMock(UnfetchedProgrammeItem::class),
            [$this->createMock(Service::class)],
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            1
        );

        $broadcast->getProgrammeItem();
    }
}
