<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\Enumeration\IsPodcastableEnum;
use PHPUnit_Framework_TestCase;

class SeriesTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('p01m5mss');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'standard', 'jpg');

        $programme = new Series(
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
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'standard', 'jpg');
        $parent = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Series'
        );
        $releaseDate = new PartialDate(2015, 01, 02);

        $programme = new Series(
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
}
