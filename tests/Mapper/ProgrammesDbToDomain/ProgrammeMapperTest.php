<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ImageMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\Enumeration\IsPodcastableEnum;
use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use DateTime;
use DateTimeImmutable;
use PHPUnit_Framework_TestCase;

class ProgrammeMapperTest extends PHPUnit_Framework_TestCase
{
    private $mapper;

    public function setup()
    {
        $this->mapper = new ProgrammeMapper(new ImageMapper());
    }

    public function testGetDomainModelBrand()
    {
        $dbEntityArray = [
            'type' => 'brand',
            'id' => '1',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'longestSynopsis' => 'Longest Synopsis',
            'image' => [
                'id' => '1',
                'pid' => 'p01m5mss',
                'title' => 'Title',
                'shortSynopsis' => 'ShortSynopsis',
                'longestSynopsis' => 'LongestSynopsis',
                'type' => 'standard',
                'extension' => 'jpg',
            ],
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'isStreamable' => true,
            'aggregatedBroadcastsCount' => 11,
            'aggregatedEpisodesCount' => 12,
            'availableClipsCount' => 13,
            'availableEpisodesCount' => 14,
            'availableGalleriesCount' => 15,
            'isPodcastable' => IsPodcastableEnum::NO,
            'parent' => null,
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 101,
            'expectedChildCount' => 1001,
        ];

        $expectedEntity = new Brand(
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            'Short Synopsis',
            'Longest Synopsis',
            new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg'),
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
            null,
            new PartialDate(2015, 01, 02),
            101,
            1001
        );

        $this->assertEquals($expectedEntity, $this->mapper->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelSeries()
    {
        $dbEntityArray = [
            'type' => 'series',
            'id' => '1',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'longestSynopsis' => 'Longest Synopsis',
            'image' => [
                'id' => '1',
                'pid' => 'p01m5mss',
                'title' => 'Title',
                'shortSynopsis' => 'ShortSynopsis',
                'longestSynopsis' => 'LongestSynopsis',
                'type' => 'standard',
                'extension' => 'jpg',
            ],
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'isStreamable' => true,
            'aggregatedBroadcastsCount' => 11,
            'aggregatedEpisodesCount' => 12,
            'availableClipsCount' => 13,
            'availableEpisodesCount' => 14,
            'availableGalleriesCount' => 15,
            'isPodcastable' => IsPodcastableEnum::NO,
            'parent' => null,
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 101,
            'expectedChildCount' => 1001,
        ];

        $expectedEntity = new Series(
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            'Short Synopsis',
            'Longest Synopsis',
            new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg'),
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
            null,
            new PartialDate(2015, 01, 02),
            101,
            1001
        );

        $this->assertEquals($expectedEntity, $this->mapper->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelEpisode()
    {
        $dbEntityArray = [
            'type' => 'episode',
            'id' => '1',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'longestSynopsis' => 'Longest Synopsis',
            'image' => [
                'id' => '1',
                'pid' => 'p01m5mss',
                'title' => 'Title',
                'shortSynopsis' => 'ShortSynopsis',
                'longestSynopsis' => 'LongestSynopsis',
                'type' => 'standard',
                'extension' => 'jpg',
            ],
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'isStreamable' => true,
            'mediaType' => MediaTypeEnum::UNKNOWN,
            'aggregatedBroadcastsCount' => 11,
            'availableClipsCount' => 12,
            'availableGalleriesCount' => 13,
            'parent' => null,
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 101,
            'duration' => 102,
            'streamableFrom' => new DateTime('2015-01-03'),
            'streamableUntil' => new DateTime('2015-01-04'),
        ];

        $expectedEntity = new Episode(
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            'Short Synopsis',
            'Longest Synopsis',
            new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg'),
            1,
            2,
            true,
            true,
            MediaTypeEnum::UNKNOWN,
            11,
            12,
            13,
            null,
            new PartialDate(2015, 01, 02),
            101,
            102,
            new DateTimeImmutable('2015-01-03'),
            new DateTimeImmutable('2015-01-04')
        );

        $this->assertEquals($expectedEntity, $this->mapper->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelClip()
    {
        $dbEntityArray = [
            'type' => 'clip',
            'id' => '1',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'longestSynopsis' => 'Longest Synopsis',
            'image' => [
                'id' => '1',
                'pid' => 'p01m5mss',
                'title' => 'Title',
                'shortSynopsis' => 'ShortSynopsis',
                'longestSynopsis' => 'LongestSynopsis',
                'type' => 'standard',
                'extension' => 'jpg',
            ],
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'isStreamable' => true,
            'mediaType' => MediaTypeEnum::UNKNOWN,
            'parent' => null,
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 101,
            'duration' => 102,
            'streamableFrom' => new DateTime('2015-01-03'),
            'streamableUntil' => new DateTime('2015-01-04'),
        ];

        $expectedEntity = new Clip(
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            'Short Synopsis',
            'Longest Synopsis',
            new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg'),
            1,
            2,
            true,
            true,
            MediaTypeEnum::UNKNOWN,
            null,
            new PartialDate(2015, 01, 02),
            101,
            102,
            new DateTimeImmutable('2015-01-03'),
            new DateTimeImmutable('2015-01-04')
        );

        $this->assertEquals($expectedEntity, $this->mapper->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelBrandUsingDefaultImage()
    {
        $dbEntityArray = [
            'type' => 'brand',
            'id' => '1',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'longestSynopsis' => 'Longest Synopsis',
            'image' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'isStreamable' => true,
            'aggregatedBroadcastsCount' => 11,
            'aggregatedEpisodesCount' => 12,
            'availableClipsCount' => 13,
            'availableEpisodesCount' => 14,
            'availableGalleriesCount' => 15,
            'isPodcastable' => IsPodcastableEnum::NO,
            'parent' => null,
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 101,
            'expectedChildCount' => 1001,
        ];

        $expectedEntity = new Brand(
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            'Short Synopsis',
            'Longest Synopsis',
            new Image(new Pid('p01tqv8z'), 'bbc_640x360.png', 'BBC Blocks for /programmes', 'BBC Blocks for /programmes', 'standard', 'png'),
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
            null,
            new PartialDate(2015, 01, 02),
            101,
            1001
        );

        $this->assertEquals($expectedEntity, $this->mapper->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelSeriesWithParentProgramme()
    {
        $dbEntityArray = [
            'type' => 'series',
            'id' => '1',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'longestSynopsis' => 'Longest Synopsis',
            'image' => [
                'id' => '1',
                'pid' => 'p01m5mss',
                'title' => 'Title',
                'shortSynopsis' => 'ShortSynopsis',
                'longestSynopsis' => 'LongestSynopsis',
                'type' => 'standard',
                'extension' => 'jpg',
            ],
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'isStreamable' => true,
            'aggregatedBroadcastsCount' => 11,
            'aggregatedEpisodesCount' => 12,
            'availableClipsCount' => 13,
            'availableEpisodesCount' => 14,
            'availableGalleriesCount' => 15,
            'isPodcastable' => IsPodcastableEnum::NO,
            'parent' => null,
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 101,
            'expectedChildCount' => 1001,
            'parent' => [
                'type' => 'brand',
                'id' => '1',
                'pid' => 'b010t19z',
                'title' => 'Title',
                'searchTitle' => 'Search Title',
                'shortSynopsis' => 'Short Synopsis',
                'longestSynopsis' => 'Longest Synopsis',
                'image' => [
                    'id' => '1',
                    'pid' => 'p01m5mss',
                    'title' => 'Title',
                    'shortSynopsis' => 'ShortSynopsis',
                    'longestSynopsis' => 'LongestSynopsis',
                    'type' => 'standard',
                    'extension' => 'jpg',
                ],
                'promotionsCount' => 1,
                'relatedLinksCount' => 2,
                'hasSupportingContent' => true,
                'isStreamable' => true,
                'aggregatedBroadcastsCount' => 11,
                'aggregatedEpisodesCount' => 12,
                'availableClipsCount' => 13,
                'availableEpisodesCount' => 14,
                'availableGalleriesCount' => 15,
                'isPodcastable' => IsPodcastableEnum::NO,
                'parent' => null,
                'releaseDate' => new PartialDate(2015, 01, 02),
                'position' => 101,
                'expectedChildCount' => 1001,
            ],
        ];

        $expectedEntity = new Series(
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            'Short Synopsis',
            'Longest Synopsis',
            new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg'),
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
            new Brand(
                new Pid('b010t19z'),
                'Title',
                'Search Title',
                'Short Synopsis',
                'Longest Synopsis',
                new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg'),
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
                null,
                new PartialDate(2015, 01, 02),
                101,
                1001
            ),
            new PartialDate(2015, 01, 02),
            101,
            1001
        );

        $this->assertEquals($expectedEntity, $this->mapper->getDomainModel($dbEntityArray));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Could not find build domain model for unknown programme type "ham"
     */
    public function testUnknownProgrammeType()
    {
        $this->mapper->getDomainModel(['type' => 'ham']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Could not find build domain model for unknown programme type ""
     */
    public function testEmptyProgrammeType()
    {
        $this->mapper->getDomainModel([]);
    }
}
