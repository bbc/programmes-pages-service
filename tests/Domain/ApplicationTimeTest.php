<?php

namespace Tests\BBC\ProgrammesPagesService\Domain;

use BBC\ProgrammesPagesService\Domain\ApplicationTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ApplicationTimeTest extends TestCase
{
    public function tearDown()
    {
        ApplicationTime::blank();
    }

    public function testDefaultValue()
    {
        $currentTime = DateTimeImmutable::createFromFormat('U', time());

        $initialTime = ApplicationTime::getTime();
        $this->assertEquals($currentTime, $initialTime);
        $this->assertEquals('Europe/London', $initialTime->getTimezone()->getName());
        $this->assertNotEquals($currentTime->format('Y-m-d H:i:s'), $initialTime->format('Y-m-d H:i:s'));
    }

    public function testSetTime()
    {
        $currentTime = DateTimeImmutable::createFromFormat('U', '1400000000');

        ApplicationTime::setTime(1400000000);

        $initialTime = ApplicationTime::getTime();
        $this->assertEquals($currentTime, $initialTime);
        $this->assertEquals('Europe/London', $initialTime->getTimezone()->getName());
        $this->assertNotEquals($currentTime->format('Y-m-d H:i:s'), $initialTime->format('Y-m-d H:i:s'));
    }

    public function testGetTimeWithUTCTimezone()
    {
        $currentTime = DateTimeImmutable::createFromFormat('U', '1400000000');

        ApplicationTime::setTime(1400000000);

        $initialTime = ApplicationTime::getTime('UTC');
        $this->assertEquals($currentTime, $initialTime);
        $this->assertEquals('UTC', $initialTime->getTimezone()->getName());
        $this->assertEquals($currentTime->format('Y-m-d H:i:s'), $initialTime->format('Y-m-d H:i:s'));
    }
}
