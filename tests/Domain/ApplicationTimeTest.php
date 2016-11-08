<?php

namespace Tests\BBC\ProgrammesPagesService\Domain;

use BBC\ProgrammesPagesService\Domain\ApplicationTime;
use PHPUnit_Framework_TestCase;
use DateTimeImmutable;

class ApplicationTimeTest extends PHPUnit_Framework_TestCase
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

        // Assert it always returns the same object - avoiding drift
        $this->assertSame($initialTime, ApplicationTime::getTime());
    }

    public function testSetTime()
    {
        $currentTime = DateTimeImmutable::createFromFormat('U', '1400000000');

        ApplicationTime::setTime(1400000000);

        $initialTime = ApplicationTime::getTime();
        $this->assertEquals($currentTime, $initialTime);

        // Assert it always returns the same object - avoiding drift
        $this->assertSame($initialTime, ApplicationTime::getTime());
    }
}
