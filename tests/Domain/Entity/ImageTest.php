<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testConstructor()
    {
        $pid = new Pid('p01m5mss');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $this->assertEquals($pid, $image->getPid());
        $this->assertEquals('Title', $image->getTitle());
        $this->assertEquals('ShortSynopsis', $image->getShortSynopsis());
        $this->assertEquals('LongestSynopsis', $image->getLongestSynopsis());
        $this->assertEquals('standard', $image->getType());
        $this->assertFalse($image->isLetterBox());

        $this->assertEquals('https://ichef.bbci.co.uk/images/ic/320xn/p01m5mss.jpg', $image->getUrl(320));
        $this->assertEquals('https://ichef.bbci.co.uk/images/ic/320x180/p01m5mss.jpg', $image->getUrl(320, 180));
    }

    public function testIsLetterBox()
    {
        $pid = new Pid('p01m5mss');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'letterbox', 'jpg');

        $this->assertTrue($image->isLetterBox());
    }

    public function testGetUrlPng()
    {
        $pid = new Pid('p01m5mss');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'png');

        $this->assertEquals('https://ichef.bbci.co.uk/images/ic/320xn/p01m5mss.png', $image->getUrl(320));
        $this->assertEquals('https://ichef.bbci.co.uk/images/ic/320x180/p01m5mss.png', $image->getUrl(320, 180));
    }
}
