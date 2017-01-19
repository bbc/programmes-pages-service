<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use PHPUnit_Framework_TestCase;
use DateTimeImmutable;

class EpisodeTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $options = new Options(['two' => 2]);

        $programme = new Episode(
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
            MediaTypeEnum::UNKNOWN,
            1201,
            1301,
            1302,
            1303,
            $options
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
        $this->assertEquals(MediaTypeEnum::UNKNOWN, $programme->getMediaType());
        $this->assertEquals(1201, $programme->getSegmentEventCount());
        $this->assertEquals(1301, $programme->getAggregatedBroadcastsCount());
        $this->assertEquals(1302, $programme->getAvailableClipsCount());
        $this->assertEquals(1303, $programme->getAvailableGalleriesCount());
        $this->assertEquals($options, $programme->getOptions());
        $this->assertSame(2, $programme->getOption('two'));
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $parent = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Series');
        $masterBrand = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\MasterBrand');
        $releaseDate = new PartialDate(2015, 01, 02);

        $genre = new Genre([0], 'id', 'Title', 'url_key');
        $format = new Format([1], 'id2', 'Title', 'url_key');

        $streamableFrom = new DateTimeImmutable();
        $streamableUntil = new DateTimeImmutable();

        $firstBroadcastDate = new DateTimeImmutable();

        $programme = new Episode(
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
            MediaTypeEnum::UNKNOWN,
            1201,
            1301,
            1302,
            1303,
            new Options([]),
            $parent,
            2101,
            $masterBrand,
            [$genre],
            [$format],
            $firstBroadcastDate,
            $releaseDate,
            2201,
            $streamableFrom,
            $streamableUntil
        );

        $this->assertEquals($parent, $programme->getParent());
        $this->assertEquals(2101, $programme->getPosition());
        $this->assertEquals($masterBrand, $programme->getMasterBrand());
        $this->assertEquals([$genre], $programme->getGenres());
        $this->assertEquals([$format], $programme->getFormats());
        $this->assertEquals($releaseDate, $programme->getReleaseDate());
        $this->assertEquals($firstBroadcastDate, $programme->getFirstBroadcastDate());
        $this->assertEquals(2201, $programme->getDuration());
        $this->assertEquals($streamableFrom, $programme->getStreamableFrom());
        $this->assertEquals($streamableUntil, $programme->getStreamableUntil());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidMediaType()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        new Episode(
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
            'wrongwrongwrong',
            1201,
            1301,
            1302,
            1303,
            new Options([])
        );
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testUnfetchedParent()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $programme = new Episode(
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
            MediaTypeEnum::UNKNOWN,
            1201,
            1301,
            1302,
            1303,
            new Options([]),
            new UnfetchedProgramme()
        );

        $programme->getParent();
    }
}
