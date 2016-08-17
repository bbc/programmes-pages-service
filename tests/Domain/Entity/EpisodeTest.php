<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
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

        $programme = new Episode(
            0,
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
            MediaTypeEnum::UNKNOWN,
            1201,
            1301,
            1302,
            1303
        );

        $this->assertEquals(0, $programme->getDbId());
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
        $this->assertEquals(MediaTypeEnum::UNKNOWN, $programme->getMediaType());
        $this->assertEquals(1201, $programme->getSegmentEventCount());
        $this->assertEquals(1301, $programme->getAggregatedBroadcastsCount());
        $this->assertEquals(1302, $programme->getAvailableClipsCount());
        $this->assertEquals(1303, $programme->getAvailableGalleriesCount());
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $parent = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Series');
        $masterBrand = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\MasterBrand');
        $releaseDate = new PartialDate(2015, 01, 02);

        $genre = new Genre('id', 'Title', 'url_key');
        $format = new Format('id2', 'Title', 'url_key');

        $streamableFrom = new DateTimeImmutable();
        $streamableUntil = new DateTimeImmutable();

        $firstBroadcastDate = new DateTimeImmutable();

        $programme = new Episode(
            0,
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
            MediaTypeEnum::UNKNOWN,
            1201,
            1301,
            1302,
            1303,
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

        $programme = new Episode(
            0,
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
            'wrongwrongwrong',
            1201,
            1301,
            1302,
            1303
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
            0,
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
            MediaTypeEnum::UNKNOWN,
            1201,
            1301,
            1302,
            1303,
            new UnfetchedProgramme()
        );

        $programme->getParent();
    }
}
