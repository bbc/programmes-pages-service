<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\Enumeration\IsPodcastableEnum;
use PHPUnit_Framework_TestCase;

class BrandTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('p01m5mss');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $programme = new Brand(
            $pid,
            'Title',
            'Search Title',
            'Short Synopsis',
            'Longest Synopsis',
            $image,
            1,
            2,
            true,
            true,
            11,
            12,
            13,
            14,
            15,
            IsPodcastableEnum::NO
        );

        $this->assertEquals($pid, $programme->getPid());
        $this->assertEquals('Title', $programme->getTitle());
        $this->assertEquals('Search Title', $programme->getSearchTitle());
        $this->assertEquals('Short Synopsis', $programme->getShortSynopsis());
        $this->assertEquals('Longest Synopsis', $programme->getLongestSynopsis());
        $this->assertEquals($image, $programme->getImage());
        $this->assertEquals(1, $programme->getPromotionsCount());
        $this->assertEquals(2, $programme->getRelatedLinksCount());
        $this->assertEquals(true, $programme->hasSupportingContent());
        $this->assertEquals(true, $programme->isStreamable());
        $this->assertEquals(11, $programme->getAggregatedBroadcastsCount());
        $this->assertEquals(12, $programme->getAggregatedEpisodesCount());
        $this->assertEquals(13, $programme->getAvailableClipsCount());
        $this->assertEquals(14, $programme->getAvailableEpisodesCount());
        $this->assertEquals(15, $programme->getAvailableGalleriesCount());
        $this->assertEquals(IsPodcastableEnum::NO, $programme->IsPodcastable());

    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $parent = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Brand'
        );
        $releaseDate = new PartialDate(2015, 01, 02);

        $programme = new Brand(
            $pid,
            'Title',
            'Search Title',
            'Short Synopsis',
            'Longest Synopsis',
            $image,
            1,
            2,
            true,
            true,
            11,
            12,
            13,
            14,
            15,
            IsPodcastableEnum::NO,
            $parent,
            $releaseDate,
            101,
            1001
        );

        $this->assertEquals($parent, $programme->getParent());
        $this->assertEquals($releaseDate, $programme->getReleaseDate());
        $this->assertEquals(101, $programme->getPosition());
        $this->assertEquals(1001, $programme->getExpectedChildCount());
    }


    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Tried to create a ProgrammeContainer with an invalid isPodcastable. Expected one of "high", "low" or "no" but got "wrongwrongwrong"
     */
    public function testInvalidMediaType()
    {
        $pid = new Pid('p01m5mss');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $pid = new Pid('p01m5mss');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $programme = new Brand(
            $pid,
            'Title',
            'Search Title',
            'Short Synopsis',
            'Longest Synopsis',
            $image,
            1,
            2,
            true,
            true,
            11,
            12,
            13,
            14,
            15,
            'wrongwrongwrong'
        );
    }
}
