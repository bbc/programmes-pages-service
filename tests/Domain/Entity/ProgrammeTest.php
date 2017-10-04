<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use PHPUnit\Framework\TestCase;

class ProgrammeTest extends TestCase
{
    public function testGetAncestryFullNoContext()
    {
        $episode = $this->getEpisode();
        $ancestry = $episode->getAncestry(null);
        $this->assertCount(3, $ancestry);
        $this->assertEquals('Episode', $ancestry[0]->getTitle());
        $this->assertEquals('Series', $ancestry[1]->getTitle());
        $this->assertEquals('Brand', $ancestry[2]->getTitle());
    }

    public function testGetAncestryFullContext()
    {
        $episode = $this->getEpisode();
        // Series is context
        $series = $episode->getParent();
        $ancestry = $episode->getAncestry($series);
        $this->assertCount(1, $ancestry);
        $this->assertEquals('Episode', $ancestry[0]->getTitle());
    }

    public function testGetAncestryStupidContext()
    {
        $episode = $this->getEpisode();
        // Self as context should still return 1 thing
        $ancestry = $episode->getAncestry($episode);
        $this->assertCount(1, $ancestry);
        $this->assertEquals('Episode', $ancestry[0]->getTitle());
    }

    public function testGetAncestryPartial()
    {
        $series = $this->getSeries();
        $ancestry = $series->getAncestry();
        $this->assertCount(2, $ancestry);
        $this->assertEquals('Series', $ancestry[0]->getTitle());
        $this->assertEquals('Brand', $ancestry[1]->getTitle());
    }

    public function testGetAncestrySingle()
    {
        $brand = $this->getBrand();
        $ancestry = $brand->getAncestry();
        $this->assertCount(1, $ancestry);
        $this->assertEquals('Brand', $ancestry[0]->getTitle());
    }

    public function testIsTleo()
    {
        $brand = $this->getBrand();
        $clip = $this->getClip();
        $episode = $this->getEpisode();
        $series = $this->getSeries();
        $this->assertTrue($brand->isTleo());
        $this->assertTrue($clip->isTleo());
        $this->assertFalse($episode->isTleo());
        $this->assertFalse($series->isTleo());
    }

    public function testIsTlec()
    {
        $brand = $this->getBrand();
        $clip = $this->getClip();
        $episode = $this->getEpisode();
        $series = $this->getSeries();
        $this->assertTrue($brand->isTlec());
        $this->assertFalse($clip->isTlec());
        $this->assertFalse($episode->isTlec());
        $this->assertFalse($series->isTlec());
    }

    private function getClip()
    {
        $pid = new Pid('p01m5msd');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        return new Clip(
            [0, 1, 2],
            $pid,
            'Clip',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            true,
            true,
            true,
            1103,
            MediaTypeEnum::UNKNOWN,
            1201,
            1104,
            new Options()
        );
    }

    private function getEpisode()
    {
        $pid = new Pid('p01m5msd');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        return new Episode(
            [0, 1, 2],
            $pid,
            'Episode',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            true,
            true,
            true,
            1103,
            MediaTypeEnum::UNKNOWN,
            1201,
            1301,
            1302,
            1303,
            new Options(),
            $this->getSeries()
        );
    }
    private function getSeries()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        return new Series(
            [0, 1, 2],
            $pid,
            'Series',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            true,
            true,
            true,
            1103,
            1201,
            1202,
            1203,
            1204,
            1205,
            false,
            new Options(),
            $this->getBrand()
        );
    }

    private function getBrand()
    {
        $pid = new Pid('p01m5msq');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        return new Brand(
            [0, 1, 2],
            $pid,
            'Brand',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            true,
            true,
            true,
            1103,
            1201,
            1202,
            1203,
            1204,
            1205,
            false,
            new Options()
        );
    }
}
