<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\BroadcastGap;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class BroadcastGapTest extends TestCase
{
    public function testConstructorRequiredArgs()
    {
        $service = $this->createMock(Service::class);
        $startAt = new DateTimeImmutable('2015-01-01 06:00:00');
        $endAt = new DateTimeImmutable('2015-01-01 07:00:00');

        $broadcastGap = new BroadcastGap(
            $service,
            $startAt,
            $endAt
        );

        $this->assertSame($service, $broadcastGap->getService());
        $this->assertSame($startAt, $broadcastGap->getStartAt());
        $this->assertSame($endAt, $broadcastGap->getEndAt());

        // Exactly at the start and a moment before - starts are inclusive
        $this->assertTrue($broadcastGap->isOnAirAt(new DateTimeImmutable('2015-01-01 06:00:00')));
        $this->assertFalse($broadcastGap->isOnAirAt(new DateTimeImmutable('2015-01-01 05:59:59')));

        // Exactly at the end and a moment after - ends are exclusive
        $this->assertTrue($broadcastGap->isOnAirAt(new DateTimeImmutable('2015-01-01 06:59:59')));
        $this->assertFalse($broadcastGap->isOnAirAt(new DateTimeImmutable('2015-01-01 07:00:00')));
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get Service of BroadcastGap as it was not fetched
     */
    public function testRequestingUnfetchedServiceThrowsException()
    {
        $broadcastGap = new BroadcastGap(
            $this->createMock(UnfetchedService::class),
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $broadcastGap->getService();
    }
}
