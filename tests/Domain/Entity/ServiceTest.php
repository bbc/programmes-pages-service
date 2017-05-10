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
        $startDate = new DateTimeImmutable('2015-01-01');
        $endDate = new DateTimeImmutable('2016-01-01');

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
}
