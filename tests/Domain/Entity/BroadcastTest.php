<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class BroadcastTest extends TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('p01m5mss');
        $version = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Version');
        $programmeItem = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem');
        $service = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Service');
        $startAt = new DateTimeImmutable();
        $endAt = new DateTimeImmutable();

        $broadcast = new Broadcast(
            $pid,
            $version,
            $programmeItem,
            $service,
            $startAt,
            $endAt,
            1
        );

        $this->assertSame($pid, $broadcast->getPid());
        $this->assertSame($version, $broadcast->getVersion());
        $this->assertSame($programmeItem, $broadcast->getProgrammeItem());
        $this->assertSame($service, $broadcast->getService());
        $this->assertSame($startAt, $broadcast->getStartAt());
        $this->assertSame($endAt, $broadcast->getEndAt());
        $this->assertSame(1, $broadcast->getDuration());
        $this->assertSame(false, $broadcast->isBlanked());
        $this->assertSame(false, $broadcast->isRepeat());
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $version = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Version');
        $programmeItem = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem');
        $service = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Service');
        $startAt = new DateTimeImmutable();
        $endAt = new DateTimeImmutable();

        $broadcast = new Broadcast(
            $pid,
            $version,
            $programmeItem,
            $service,
            $startAt,
            $endAt,
            1,
            true,
            true
        );

        $this->assertSame(true, $broadcast->isBlanked());
        $this->assertSame(true, $broadcast->isRepeat());
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get Version of Broadcast "p01m5mss" as it was not fetched
     */
    public function testRequestingUnfetchedVersionThrowsException()
    {
        $broadcast = new Broadcast(
            new Pid('p01m5mss'),
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion'),
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem'),
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Service'),
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            1
        );

        $broadcast->getVersion();
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get ProgrammeItem of Broadcast "p01m5mss" as it was not fetched
     */
    public function testRequestingUnfetchedProgrammeItemThrowsException()
    {
        $broadcast = new Broadcast(
            new Pid('p01m5mss'),
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Version'),
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgrammeItem'),
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Service'),
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            1
        );

        $broadcast->getProgrammeItem();
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get Service of Broadcast "p01m5mss" as it was not fetched
     */
    public function testRequestingUnfetchedServiceThrowsException()
    {
        $broadcast = new Broadcast(
            new Pid('p01m5mss'),
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Version'),
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem'),
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedService'),
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            1
        );

        $broadcast->getService();
    }
}
