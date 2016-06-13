<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;
use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use PHPUnit_Framework_TestCase;
use DateTimeImmutable;

class ClipTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('p01m5mss');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $programme = new Clip(
            0,
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
            MediaTypeEnum::UNKNOWN
        );

        $this->assertEquals(0, $programme->getDbId());
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
        $this->assertEquals(MediaTypeEnum::UNKNOWN, $programme->getMediaType());
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $parent = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Series');
        $masterBrand = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\MasterBrand');
        $releaseDate = new PartialDate(2015, 01, 02);

        $genre = new Genre('id', 'Title', 'url_key');
        $format = new Format('id2', 'Title', 'url_key');

        $streamableFrom = new DateTimeImmutable();
        $streamableUntil = new DateTimeImmutable();

        $programme = new Clip(
            0,
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
            MediaTypeEnum::UNKNOWN,
            $parent,
            101,
            $masterBrand,
            [$genre],
            [$format],
            $releaseDate,
            1001,
            $streamableFrom,
            $streamableUntil
        );

        $this->assertEquals($parent, $programme->getParent());
        $this->assertEquals($releaseDate, $programme->getReleaseDate());
        $this->assertEquals(101, $programme->getPosition());
        $this->assertEquals($masterBrand, $programme->getMasterBrand());
        $this->assertEquals([$genre], $programme->getGenres());
        $this->assertEquals([$format], $programme->getFormats());
        $this->assertEquals(1001, $programme->getDuration());
        $this->assertEquals($streamableFrom, $programme->getStreamableFrom());
        $this->assertEquals($streamableUntil, $programme->getStreamableUntil());
    }
}
