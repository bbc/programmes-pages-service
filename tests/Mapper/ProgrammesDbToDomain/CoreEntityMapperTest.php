<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Entity\Collection;
use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Franchise;
use BBC\ProgrammesPagesService\Domain\Entity\Gallery;
use BBC\ProgrammesPagesService\Domain\Entity\Season;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use DateTime;
use DateTimeImmutable;

class CoreEntityMapperTest extends BaseCoreEntityMapperTestCase
{
    public function testGetDomainModelBrand()
    {
        $dbEntityArray = [
            'id' => 1,
            'type' => 'brand',
            'ancestry' => '1,',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'mediumSynopsis' => 'Mediumest Synopsis',
            'longSynopsis' => 'Longest Synopsis',
            'image' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'streamable' => true,
            'streamableAlternate' => true,
            'contributionsCount' => 10,
            'aggregatedBroadcastsCount' => 11,
            'aggregatedEpisodesCount' => 12,
            'availableClipsCount' => 13,
            'availableEpisodesCount' => 14,
            'aggregatedGalleriesCount' => 15,
            'isPodcastable' => false,
            'parent' => null,
            'position' => 101,
            'masterBrand' => null,
            'firstBroadcastDate' => new DateTime('2017-01-03T18:00:00Z'),
            'expectedChildCount' => 1001,
            'options' => [],
        ];

        $expectedEntity = new Brand(
            [1],
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $this->mockDefaultImage,
            1,
            2,
            true,
            true,
            true,
            10,
            11,
            12,
            13,
            14,
            15,
            false,
            $this->mockOptions,
            null,
            101,
            null,
            null,
            null,
            new DateTimeImmutable('2017-01-03T18:00:00Z'),
            1001
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModelForProgramme($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModelForProgramme($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    public function testGetDomainModelSeries()
    {
        $dbEntityArray = [
            'id' => 1,
            'type' => 'series',
            'ancestry' => '1,',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'mediumSynopsis' => 'Mediumest Synopsis',
            'longSynopsis' => 'Longest Synopsis',
            'image' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'streamable' => true,
            'streamableAlternate' => true,
            'contributionsCount' => 10,
            'aggregatedBroadcastsCount' => 11,
            'aggregatedEpisodesCount' => 12,
            'availableClipsCount' => 13,
            'availableEpisodesCount' => 14,
            'aggregatedGalleriesCount' => 15,
            'isPodcastable' => false,
            'parent' => null,
            'position' => 101,
            'masterBrand' => null,
            'firstBroadcastDate' => new DateTime('2017-01-03T18:00:00Z'),
            'expectedChildCount' => 1001,
            'options' => [],
        ];

        $expectedEntity = new Series(
            [1],
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $this->mockDefaultImage,
            1,
            2,
            true,
            true,
            true,
            10,
            11,
            12,
            13,
            14,
            15,
            false,
            $this->mockOptions,
            null,
            101,
            null,
            null,
            null,
            new DateTimeImmutable('2017-01-03T18:00:00Z'),
            1001
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModelForProgramme($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModelForProgramme($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    public function testGetDomainModelEpisode()
    {
        $dbEntityArray = [
            'id' => 1,
            'type' => 'episode',
            'ancestry' => '1,',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'mediumSynopsis' => 'Mediumest Synopsis',
            'longSynopsis' => 'Longest Synopsis',
            'image' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'streamable' => true,
            'streamableAlternate' => true,
            'contributionsCount' => 10,
            'mediaType' => MediaTypeEnum::UNKNOWN,
            'segmentEventCount' => 11,
            'aggregatedBroadcastsCount' => 101,
            'availableClipsCount' => 102,
            'aggregatedGalleriesCount' => 103,
            'parent' => null,
            'firstBroadcastDate' => new DateTime('2017-01-03T18:00:00Z'),
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 1001,
            'masterBrand' => null,
            'duration' => 1002,
            'streamableFrom' => new DateTime('2015-01-03'),
            'streamableUntil' => new DateTime('2015-01-04'),
            'options' => [],
            'downloadableMediaSets' => ['media_set_a', 'media_set_b'],
        ];

        $expectedEntity = new Episode(
            [1],
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $this->mockDefaultImage,
            1,
            2,
            true,
            true,
            true,
            10,
            MediaTypeEnum::UNKNOWN,
            11,
            101,
            102,
            103,
            $this->mockOptions,
            null,
            1001,
            null,
            null,
            null,
            new DateTimeImmutable('2017-01-03T18:00:00Z'),
            new PartialDate(2015, 01, 02),
            1002,
            new DateTimeImmutable('2015-01-03'),
            new DateTimeImmutable('2015-01-04'),
            ['media_set_a', 'media_set_b']
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModelForProgramme($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModelForProgramme($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    public function testGetDomainModelClip()
    {
        $dbEntityArray = [
            'id' => 3,
            'type' => 'clip',
            'ancestry' => '1,2,3,',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'mediumSynopsis' => 'Mediumest Synopsis',
            'longSynopsis' => 'Longest Synopsis',
            'image' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'streamable' => true,
            'streamableAlternate' => false,
            'contributionsCount' => 10,
            'mediaType' => MediaTypeEnum::UNKNOWN,
            'segmentEventCount' => 11,
            'aggregatedGalleriesCount' => 12,
            'parent' => null,
            'firstBroadcastDate' => new DateTime('2017-01-03T18:00:00Z'),
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 1001,
            'masterBrand' => null,
            'duration' => 1002,
            'streamableFrom' => new DateTime('2015-01-03'),
            'streamableUntil' => new DateTime('2015-01-04'),
            'options' => [],
        ];

        $expectedEntity = new Clip(
            [1, 2, 3],
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $this->mockDefaultImage,
            1,
            2,
            true,
            true,
            false,
            10,
            MediaTypeEnum::UNKNOWN,
            11,
            12,
            $this->mockOptions,
            null,
            1001,
            null,
            null,
            null,
            new DateTimeImmutable('2017-01-03T18:00:00Z'),
            new PartialDate(2015, 01, 02),
            1002,
            new DateTimeImmutable('2015-01-03'),
            new DateTimeImmutable('2015-01-04')
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModelForProgramme($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModelForProgramme($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    public function testGetDomainModelCollection()
    {
        $dbEntityArray = [
            'id' => 1,
            'type' => 'collection',
            'ancestry' => '1,',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'mediumSynopsis' => 'Mediumest Synopsis',
            'longSynopsis' => 'Longest Synopsis',
            'image' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'contributionsCount' => 10,
            'isPodcastable' => false,
            'parent' => null,
            'masterBrand' => null,
            'options' => [],
        ];

        $expectedEntity = new Collection(
            [1],
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $this->mockDefaultImage,
            1,
            2,
            10,
            $this->mockOptions,
            false,
            null,
            null
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModelForGroup($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModelForGroup($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    public function testGetDomainModelFranchise()
    {
        $dbEntityArray = [
            'id' => 1,
            'type' => 'franchise',
            'ancestry' => '1,',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'mediumSynopsis' => 'Mediumest Synopsis',
            'longSynopsis' => 'Longest Synopsis',
            'image' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'contributionsCount' => 10,
            'aggregatedBroadcastsCount' => 100,
            'parent' => null,
            'masterBrand' => null,
            'options' => [],
        ];

        $expectedEntity = new Franchise(
            [1],
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $this->mockDefaultImage,
            1,
            2,
            10,
            $this->mockOptions,
            100,
            null
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModelForGroup($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModelForGroup($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    public function testGetDomainModelGallery()
    {
        $dbEntityArray = [
            'id' => 1,
            'type' => 'gallery',
            'ancestry' => '1,',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'mediumSynopsis' => 'Mediumest Synopsis',
            'longSynopsis' => 'Longest Synopsis',
            'image' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'contributionsCount' => 10,
            'parent' => null,
            'masterBrand' => null,
            'options' => [],
        ];

        $expectedEntity = new Gallery(
            [1],
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $this->mockDefaultImage,
            1,
            2,
            10,
            $this->mockOptions,
            null
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModelForGroup($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModelForGroup($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    public function testGetDomainModelSeason()
    {
        $dbEntityArray = [
            'id' => 1,
            'type' => 'season',
            'ancestry' => '1,',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'mediumSynopsis' => 'Mediumest Synopsis',
            'longSynopsis' => 'Longest Synopsis',
            'image' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'contributionsCount' => 10,
            'aggregatedBroadcastsCount' => 100,
            'startDate' => new DateTime('2015-01-03'),
            'endDate' => new DateTime('2015-01-03'),
            'parent' => null,
            'masterBrand' => null,
            'options' => [],
        ];

        $expectedEntity = new Season(
            [1],
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $this->mockDefaultImage,
            1,
            2,
            10,
            $this->mockOptions,
            100,
            null
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModelForGroup($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModelForGroup($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    public function testGetDomainModelSeriesWithParentProgramme()
    {
        $dbEntityArray = [
            'id' => 2,
            'type' => 'series',
            'ancestry' => '1,2,',
            'pid' => 'b010t19z',
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'mediumSynopsis' => 'Mediumest Synopsis',
            'longSynopsis' => 'Longest Synopsis',
            'image' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'streamable' => true,
            'streamableAlternate' => false,
            'contributionsCount' => 10,
            'aggregatedBroadcastsCount' => 11,
            'aggregatedEpisodesCount' => 12,
            'availableClipsCount' => 13,
            'availableEpisodesCount' => 14,
            'aggregatedGalleriesCount' => 15,
            'isPodcastable' => false,
            'position' => 101,
            'masterBrand' => null,
            'firstBroadcastDate' => new DateTimeImmutable('2017-01-03T18:00:00Z'),
            'expectedChildCount' => 1001,
            'options' => [],
            'parent' => [
                'id' => 1,
                'type' => 'brand',
                'ancestry' => '1,',
                'pid' => 'b010t19z',
                'title' => 'Title',
                'searchTitle' => 'Search Title',
                'shortSynopsis' => 'Short Synopsis',
                'mediumSynopsis' => 'Mediumest Synopsis',
                'longSynopsis' => 'Longest Synopsis',
                'image' => null,
                'promotionsCount' => 1,
                'relatedLinksCount' => 2,
                'hasSupportingContent' => true,
                'streamable' => true,
                'streamableAlternate' => false,
                'contributionsCount' => 10,
                'aggregatedBroadcastsCount' => 11,
                'aggregatedEpisodesCount' => 12,
                'availableClipsCount' => 13,
                'availableEpisodesCount' => 14,
                'aggregatedGalleriesCount' => 15,
                'isPodcastable' => false,
                'parent' => null,
                'position' => 101,
                'masterBrand' => null,
                'firstBroadcastDate' => new DateTimeImmutable('2017-01-03T18:00:00Z'),
                'expectedChildCount' => 1001,
                'options' => [],
            ],
        ];

        $expectedEntity = new Series(
            [1, 2],
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $this->mockDefaultImage,
            1,
            2,
            true,
            true,
            false,
            10,
            11,
            12,
            13,
            14,
            15,
            false,
            $this->mockOptions,
            new Brand(
                [1],
                new Pid('b010t19z'),
                'Title',
                'Search Title',
                new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
                $this->mockDefaultImage,
                1,
                2,
                true,
                true,
                false,
                10,
                11,
                12,
                13,
                14,
                15,
                false,
                $this->mockOptions,
                null,
                101,
                null,
                null,
                null,
                new DateTimeImmutable('2017-01-03T18:00:00Z'),
                1001
            ),
            101,
            null,
            null,
            null,
            new DateTimeImmutable('2017-01-03T18:00:00Z'),
            1001
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Could not build domain model for unknown programme type "collection"
     */
    public function testUnknownProgrammeType()
    {
        $this->getMapper()->getDomainModelForProgramme(['id' => 1, 'type' => 'collection']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Could not build domain model for unknown group type "episode"
     */
    public function testUnknownGroupType()
    {
        $this->getMapper()->getDomainModelForGroup(['id' => 1, 'type' => 'episode']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unrecognized Core Entity
     */
    public function testUnknownType()
    {
        $this->getMapper()->getDomainModel(['id' => 1]);
    }
}
