<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Lease;
use PHPUnit_Framework_TestCase;

class LeaseTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $lease = new Lease();

        $this->assertSame(null, $lease->getId());
        $this->assertSame('unassigned', $lease->getWorkerId());
        $this->assertInstanceOf('\Datetime', $lease->getLeaseExpiration());
    }

    public function testSetters()
    {
        $lease = new Lease();
        $lease->setWorkerId('worker-1');
        $this->assertSame('worker-1', $lease->getWorkerId());

        $now = new \DateTime('now');
        $lease->setLeaseExpiration($now);
        $this->assertSame($now, $lease->getLeaseExpiration());
    }
}
