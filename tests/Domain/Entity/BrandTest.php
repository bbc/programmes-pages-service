<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class BrandTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $programme = new Brand(
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
        $this->assertEquals(1201, $programme->getAggregatedBroadcastsCount());
        $this->assertEquals(1202, $programme->getAggregatedEpisodesCount());
        $this->assertEquals(1203, $programme->getAvailableClipsCount());
        $this->assertEquals(1204, $programme->getAvailableEpisodesCount());
        $this->assertEquals(1205, $programme->getAvailableGalleriesCount());
        $this->assertEquals(false, $programme->isPodcastable());

    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $parent = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Brand');
        $masterBrand = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\MasterBrand');

        $genre = new Genre('id', 'Title', 'url_key');
        $format = new Format('id2', 'Title', 'url_key');

        $firstBroadcastDate = new \DateTimeImmutable();

        $programme = new Brand(
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

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Tried to create a Programme with invalid genres. Expected an array of BBC\ProgrammesPagesService\Domain\Entity\Genre but the array contained an instance of "string"
     */
    public function testInvalidGenres()
    {
        $pid = new Pid('p01m5mss');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');

        $programme = new Brand(
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
            1201,
            1202,
            1203,
            1204,
            1205,
            false,
            null,
            2101,
            null,
            ['wrongwrongwrong'],
            []
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Tried to create a Programme with invalid formats. Expected an array of BBC\ProgrammesPagesService\Domain\Entity\Format but the array contained an instance of "string"
     */
    public function testInvalidFormats()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $programme = new Brand(
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
            1201,
            1202,
            1203,
            1204,
            1205,
            false,
            null,
            2101,
            null,
            [],
            ['wrongwrongwrong']
        );
    }
}
