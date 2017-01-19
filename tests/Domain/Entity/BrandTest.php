<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedMasterBrand;
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
        $options = new Options(['one' => 1]);

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
            1103,
            1201,
            1202,
            1203,
            1204,
            1205,
            false,
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
        $options = new Options(['one' => 1]);

        $parent = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Brand');
        $masterBrand = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\MasterBrand');

        $genre = new Genre([0], 'id', 'Title', 'url_key');
        $format = new Format([1], 'id2', 'Title', 'url_key');

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
            1103,
            1201,
            1202,
            1203,
            1204,
            1205,
            false,
            $options,
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
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get MasterBrand of Programme "p01m5mss" as it was not fetched
     */
    public function testRequestingUnfetchedMasterBrandThrowsException()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $options = new Options(['one' => 1]);

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
            1103,
            1201,
            1202,
            1203,
            1204,
            1205,
            false,
            $options,
            null,
            2101,
            new UnfetchedMasterBrand()
        );

        $programme->getMasterBrand();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Tried to create a Programme with invalid genres. Expected an array of BBC\ProgrammesPagesService\Domain\Entity\Genre but the array contained an instance of "string"
     */
    public function testInvalidGenres()
    {
        $this->createBrandWithGenresAndFormats(['wrongwrongwrong'], []);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Tried to create a Programme with invalid formats. Expected an array of BBC\ProgrammesPagesService\Domain\Entity\Format but the array contained an instance of "string"
     */
    public function testInvalidFormats()
    {
        $this->createBrandWithGenresAndFormats([], ['wrongwrongwrong']);
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get Genres of Programme "p01m5mss" as they were not fetched
     */
    public function testRequestingUnfetchedGenresThrowsException()
    {
        $brand = $this->createBrandWithGenresAndFormats(null, []);
        $brand->getGenres();
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get Formats of Programme "p01m5mss" as they were not fetched
     */
    public function testRequestingUnfetchedFormatsThrowsException()
    {
        $brand = $this->createBrandWithGenresAndFormats([], null);
        $brand->getFormats();
    }

    private function createBrandWithGenresAndFormats(?array $genres, ?array $formats)
    {
        return new Brand(
            [0],
            new Pid('p01m5mss'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Longest Synopsis', ''),
            new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg'),
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
            new Options([]),
            null,
            2101,
            null,
            $genres,
            $formats
        );
    }
}
