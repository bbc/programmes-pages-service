<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use PHPUnit_Framework_TestCase;

class SeriesTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $programme = new Series(
            [0, 1, 2],
            $pid,
            'Title',
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
            false
        );

        $this->assertEquals(2, $programme->getDbId());
        $this->assertEquals([0, 1, 2], $programme->getDbAncestryIds());
        $this->assertEquals($pid, $programme->getPid());
        $this->assertEquals('Title', $programme->getTitle());
        $this->assertEquals('Search Title', $programme->getSearchTitle());
        $this->assertEquals($synopses, $programme->getSynopses());
        $this->assertEquals('Short Synopsis', $programme->getShortSynopsis());
        $this->assertEquals('Longest Synopsis', $programme->getLongestSynopsis());
        $this->assertEquals($image, $programme->getImage());
        $this->assertEquals(1101, $programme->getPromotionsCount());
        $this->assertEquals(1102, $programme->getRelatedLinksCount());
        $this->assertEquals(true, $programme->hasSupportingContent());
        $this->assertEquals(true, $programme->isStreamable());
        $this->assertEquals(true, $programme->isStreamableAlternatate());
        $this->assertEquals(1103, $programme->getContributionsCount());
        $this->assertEquals(1201, $programme->getAggregatedBroadcastsCount());
        $this->assertEquals(1202, $programme->getAggregatedEpisodesCount());
        $this->assertEquals(1203, $programme->getAvailableClipsCount());
        $this->assertEquals(1204, $programme->getAvailableEpisodesCount());
        $this->assertEquals(1205, $programme->getAvailableGalleriesCount());
        $this->assertEquals(false, $programme->IsPodcastable());

    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $parent = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Series');
        $masterBrand = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\MasterBrand');

        $genre = new Genre([0], 'id', 'Title', 'url_key');
        $format = new Format('id2', 'Title', 'url_key');

        $firstBroadcastDate = new \DateTimeImmutable();

        $programme = new Series(
            [0],
            $pid,
            'Title',
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
            $parent,
            2101,
            $masterBrand,
            [$genre],
            [$format],
            $firstBroadcastDate,
            2201
        );

        $this->assertEquals($parent, $programme->getParent());
        $this->assertEquals(2101, $programme->getPosition());
        $this->assertEquals($masterBrand, $programme->getMasterBrand());
        $this->assertEquals([$genre], $programme->getGenres());
        $this->assertEquals([$format], $programme->getFormats());
        $this->assertEquals($firstBroadcastDate, $programme->getFirstBroadcastDate());
        $this->assertEquals(2201, $programme->getExpectedChildCount());
    }
}
