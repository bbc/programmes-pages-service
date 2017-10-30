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
        $this->assertEquals('UTC', $initialTime->getTimezone()->getName());
        // Assert it always returns the same object - avoiding drift
        $this->assertSame($initialTime, ApplicationTime::getTime());
    }

    public function testSetTime()
    {
        $currentTime = DateTimeImmutable::createFromFormat('U', '1400000000');

        ApplicationTime::setTime(1400000000);

        $initialTime = ApplicationTime::getTime();
        $this->assertEquals($currentTime, $initialTime);
        $this->assertEquals('UTC', $initialTime->getTimezone()->getName());
        // Assert it always returns the same object - avoiding drift
        $this->assertSame($initialTime, ApplicationTime::getTime());
    }

    public function testLocalTimeDefaultValue()
    {
        $currentTime = DateTimeImmutable::createFromFormat('U', time());

        $initialTime = ApplicationTime::getLocalTime();
        $this->assertEquals($currentTime, $initialTime);
        $this->assertEquals('Europe/London', $initialTime->getTimezone()->getName());
    }

    public function testLocalTimeSetTime()
    {
        $currentTime = DateTimeImmutable::createFromFormat('U', '1400000000');

        ApplicationTime::setTime(1400000000);

        $initialTime = ApplicationTime::getLocalTime();
        $this->assertEquals($currentTime, $initialTime);
        $this->assertEquals('Europe/London', $initialTime->getTimezone()->getName());
        $this->assertNotEquals($currentTime->format('Y-m-d H:i:s'), $initialTime->format('Y-m-d H:i:s'));
    }

    public function testLocalTimeGetTimeWithUTCTimezone()
    {
        $currentTime = DateTimeImmutable::createFromFormat('U', '1400000000');

        ApplicationTime::setTime(1400000000);

        ApplicationTime::setLocalTimeZone('UTC');
        $initialTime = ApplicationTime::getLocalTime();
        $this->assertEquals($currentTime, $initialTime);
        $this->assertEquals('UTC', $initialTime->getTimezone()->getName());
        $this->assertEquals($currentTime->format('Y-m-d H:i:s'), $initialTime->format('Y-m-d H:i:s'));
    }

    public function testLocalTimeIsSetWithTime()
    {
        $oldTime = DateTimeImmutable::createFromFormat('U', '1400000000');
        ApplicationTime::setTime(1400000000);
        $initialTime = ApplicationTime::getLocalTime();

        $newTime = DateTimeImmutable::createFromFormat('U', '1500000000');
        ApplicationTime::setTime(1500000000);
        $newLocalTime = ApplicationTime::getLocalTime();

        $this->assertNotEquals($initialTime, $newLocalTime);
        $this->assertEquals($newTime->getTimestamp(), $newLocalTime->getTimestamp());
    }
}
