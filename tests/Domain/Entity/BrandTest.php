<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\RelatedLink;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;
use InvalidArgumentException;
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
            false
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
        $this->assertEquals(false, $programme->IsPodcastable());

    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $parent = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Brand'
        );
        $masterBrand = new MasterBrand(new Mid('bbc_one'), 'BBC One', $image);
        $releaseDate = new PartialDate(2015, 01, 02);

        $genre = new Genre('Title', 'url_key');
        $format = new Format('Title', 'url_key');
        $relatedLink = new RelatedLink('Title', 'http://example.com', '', '', '', false);

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
            false,
            $parent,
            $releaseDate,
            101,
            $masterBrand,
            [$genre],
            [$format],
            [$relatedLink],
            1001
        );

        $this->assertEquals($parent, $programme->getParent());
        $this->assertEquals($releaseDate, $programme->getReleaseDate());
        $this->assertEquals(101, $programme->getPosition());
        $this->assertEquals($masterBrand, $programme->getMasterBrand());
        $this->assertEquals([$genre], $programme->getGenres());
        $this->assertEquals([$format], $programme->getFormats());
        $this->assertEquals([$relatedLink], $programme->getRelatedLinks());
        $this->assertEquals(1001, $programme->getExpectedChildCount());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Tried to create a Programme with invalid genres. Expected an array of BBC\ProgrammesPagesService\Domain\Entity\Genre but the array contained an instance of "string"
     */
    public function testInvalidGenres()
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
            false,
            null,
            null,
            101,
            null,
            ['wrongwrongwrong'],
            [],
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
            false,
            null,
            null,
            101,
            null,
            [],
            ['wrongwrongwrong'],
            []
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Tried to create a Programme with invalid relatedLinks. Expected an array of BBC\ProgrammesPagesService\Domain\Entity\RelatedLink but the array contained an instance of "string"
     */
    public function testInvalidRelatedLinks()
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
            false,
            null,
            null,
            101,
            null,
            [],
            [],
            ['wrongwrongwrong']
        );
    }
}
