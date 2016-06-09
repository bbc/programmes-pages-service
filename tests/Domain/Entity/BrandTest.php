<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
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
            11,
            12,
            13,
            14,
            15,
            false
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
        $masterBrand = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\MasterBrand'
        );

        $genre = new Genre('Title', 'url_key');
        $format = new Format('Title', 'url_key');

        $programme = new Brand(
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
            11,
            12,
            13,
            14,
            15,
            false,
            $parent,
            101,
            $masterBrand,
            [$genre],
            [$format],
            1001
        );

        $this->assertEquals($parent, $programme->getParent());
        $this->assertEquals(101, $programme->getPosition());
        $this->assertEquals($masterBrand, $programme->getMasterBrand());
        $this->assertEquals([$genre], $programme->getGenres());
        $this->assertEquals([$format], $programme->getFormats());
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
            11,
            12,
            13,
            14,
            15,
            false,
            null,
            101,
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
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $programme = new Brand(
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
            11,
            12,
            13,
            14,
            15,
            false,
            null,
            101,
            null,
            [],
            ['wrongwrongwrong']
        );
    }
}
