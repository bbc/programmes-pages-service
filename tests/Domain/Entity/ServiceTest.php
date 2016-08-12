<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTimeImmutable;
use PHPUnit_Framework_TestCase;

class ServiceTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $sid = new Sid('bbc_1xtra');

        $service = new Service(
            0,
            $sid,
            'Name'
        );

        $this->assertEquals(0, $service->getDbId());
        $this->assertEquals($sid, $service->getSid());
        $this->assertEquals('Name', $service->getName());
        $this->assertEquals('Name', $service->getShortName());
        $this->assertSame('bbc_1xtra', $service->getUrlKey());
    }

    public function testConstructorOptionalArgs()
    {
        $sid = new Sid('bbc_1xtra');
        $network = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Network');
        $startDate = new DateTimeImmutable('2015-01-01');
        $endDate = new DateTimeImmutable('2016-01-01');

        $service = new Service(
            0,
            $sid,
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
}
