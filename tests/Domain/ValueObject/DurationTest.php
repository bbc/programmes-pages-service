<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\ValueObject\Duration;
use PHPUnit\Framework\TestCase;

class DurationTest extends TestCase
{
    public function testValidDuration()
    {
        $seconds = 93784;
        $duration = new Duration('P1DT2H3M4S');
        $this->assertSame(93784, $duration->getSeconds());
        $this->assertSame('93784', $duration->formatMySql());
        $this->assertSame('[93784]', json_encode([$duration]));

        $this->assertSame('1 days 2 hours 3 minutes 4 seconds', (string) $duration);
    }
}
