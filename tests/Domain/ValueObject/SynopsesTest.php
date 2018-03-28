<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use PHPUnit\Framework\TestCase;

class SynopsesTest extends TestCase
{
    public function testSynopses()
    {
        $synopses = new Synopses('short', 'medium', 'long');
        $this->assertEquals('short', $synopses->getShortSynopsis());
        $this->assertEquals('medium', $synopses->getMediumSynopsis());
        $this->assertEquals('long', $synopses->getLongSynopsis());
    }

    public function testLongestSynopsis()
    {
        $synopses = new Synopses('short', 'medium', 'long');
        $this->assertEquals('long', $synopses->getLongestSynopsis());

        $synopses = new Synopses('short', 'medium', '');
        $this->assertEquals('medium', $synopses->getLongestSynopsis());

        $synopses = new Synopses('short', '', '');
        $this->assertEquals('short', $synopses->getLongestSynopsis());
    }

    public function testShortestSynopsis()
    {
        $synopses = new Synopses('short', 'medium', 'long');
        $this->assertEquals('short', $synopses->getShortestSynopsis());

        $synopses = new Synopses('', 'medium', 'long');
        $this->assertEquals('medium', $synopses->getShortestSynopsis());

        $synopses = new Synopses('', '', 'long');
        $this->assertEquals('long', $synopses->getShortestSynopsis());
    }
}
