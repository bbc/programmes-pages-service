<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedNetwork;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    public function testConstructorRequiredArgs()
    {
        $sid = new Sid('bbc_1xtra');
        $pid = new Pid('b0000001');

        $service = new Service(
            0,
            $sid,
            $pid,
            'Name'
        );

        $this->assertEquals(0, $service->getDbId());
        $this->assertEquals($sid, $service->getSid());
        $this->assertEquals($pid, $service->getPid());
        $this->assertEquals('Name', $service->getName());
        $this->assertEquals('Name', $service->getShortName());
        $this->assertSame('bbc_1xtra', $service->getUrlKey());
    }

    public function testConstructorOptionalArgs()
    {
        $sid = new Sid('bbc_1xtra');
        $pid = new Pid('b0000001');
        $network = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Network');
        $startDate = new DateTimeImmutable('2015-01-01 06:00:00');
        $endDate = new DateTimeImmutable('2016-01-01 06:00:00');

        $service = new Service(
            0,
            $sid,
            $pid,
            'Name',
            'shortName',
            'urlKey',
            $network,
            $startDate,
            $endDate,
            'liveStreamUrl'
        );

        $this->assertEquals('shortName', $service->getShortName());
        $this->assertEquals('urlKey', $service->getUrlKey());
        $this->assertEquals($network, $service->getNetwork());
        $this->assertEquals($startDate, $service->getStartDate());
        $this->assertEquals($endDate, $service->getEndDate());
        $this->assertEquals('liveStreamUrl', $service->getLiveStreamUrl());

        // Exactly at the start and a moment before - starts are inclusive
        $this->assertTrue($service->isActiveAt(new DateTimeImmutable('2015-01-01 06:00:00')));
        $this->assertFalse($service->isActiveAt(new DateTimeImmutable('2015-01-01 05:59:59')));

        // Exactly at the end and a moment after - ends are exclusive
        $this->assertTrue($service->isActiveAt(new DateTimeImmutable('2016-01-01 05:59:00')));
        $this->assertFalse($service->isActiveAt(new DateTimeImmutable('2016-01-01 06:00:00')));
    }

    public function testIsActiveAtWithIndefiniteEnd()
    {
        $service = $this->serviceWithDates(new DateTimeImmutable('2015-01-01 06:00:00'), null);

        // Exactly at the start and a moment before - starts are inclusive
        $this->assertTrue($service->isActiveAt(new DateTimeImmutable('2015-01-01 06:00:00')));
        $this->assertFalse($service->isActiveAt(new DateTimeImmutable('2015-01-01 05:59:59')));
    }

    public function testIsActiveAtWithIndefiniteStart()
    {
        $service = $this->serviceWithDates(null, new DateTimeImmutable('2016-01-01 06:00:00'));

        // Exactly at the end and a moment after - ends are exclusive
        $this->assertTrue($service->isActiveAt(new DateTimeImmutable('2016-01-01 05:59:00')));
        $this->assertFalse($service->isActiveAt(new DateTimeImmutable('2016-01-01 06:00:00')));
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get Network of Service "bbc_1xtra" as it was not fetched
     */
    public function testRequestingUnfetchedNetworkThrowsException()
    {
        $service = new Service(
            0,
            new Sid('bbc_1xtra'),
            new Pid('b0000001'),
            'Name',
            'shortName',
            'urlKey',
            new UnfetchedNetwork()
        );

        $service->getNetwork();
    }

    private function serviceWithDates(?DateTimeImmutable $start, ?DateTimeImmutable $end): Service
    {
        return new Service(
            0,
            new Sid('bbc_1xtra'),
            new Pid('b0000001'),
            'Name',
            'shortName',
            'urlKey',
            $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Network'),
            $start,
            $end
        );
    }
}
