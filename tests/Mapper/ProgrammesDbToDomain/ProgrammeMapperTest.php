<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use DateTime;
use DateTimeImmutable;

class ProgrammeMapperTest extends BaseProgrammeMapperTestCase
{
    public function testGetDomainModelBrand()
    {
        $dbEntityArray = [
            'type' => 'brand',
            'id' => '1',
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
            'aggregatedBroadcastsCount' => 11,
            'aggregatedEpisodesCount' => 12,
            'availableClipsCount' => 13,
            'availableEpisodesCount' => 14,
            'availableGalleriesCount' => 15,
            'isPodcastable' => false,
            'parent' => null,
            'position' => 101,
            'firstBroadcastDate' => new \DateTime(),
            'expectedChildCount' => 1001,
        ];

        $expectedEntity = new Brand(
            1,
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $this->mockDefaultImage,
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
            [],
            new DateTimeImmutable(),
            1001
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
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
            'mediumSynopsis' => 'Mediumest Synopsis',
            'longSynopsis' => 'Longest Synopsis',
            'image' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'streamable' => true,
            'aggregatedBroadcastsCount' => 11,
            'aggregatedEpisodesCount' => 12,
            'availableClipsCount' => 13,
            'availableEpisodesCount' => 14,
            'availableGalleriesCount' => 15,
            'isPodcastable' => false,
            'parent' => null,
            'position' => 101,
            'firstBroadcastDate' => new DateTime(),
            'expectedChildCount' => 1001,
        ];

        $expectedEntity = new Series(
            1,
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $this->mockDefaultImage,
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
            [],
            new DateTimeImmutable(),
            1001
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
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
            'mediumSynopsis' => 'Mediumest Synopsis',
            'longSynopsis' => 'Longest Synopsis',
            'image' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'streamable' => true,
            'mediaType' => MediaTypeEnum::UNKNOWN,
            'aggregatedBroadcastsCount' => 11,
            'availableClipsCount' => 12,
            'availableGalleriesCount' => 13,
            'parent' => null,
            'firstBroadcastDate' => new DateTime(),
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 101,
            'duration' => 102,
            'streamableFrom' => new DateTime('2015-01-03'),
            'streamableUntil' => new DateTime('2015-01-04'),
        ];

        $expectedEntity = new Episode(
            1,
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $this->mockDefaultImage,
            1,
            2,
            true,
            true,
            MediaTypeEnum::UNKNOWN,
            11,
            12,
            13,
            null,
            101,
            null,
            [],
            [],
            new DateTimeImmutable(),
            new PartialDate(2015, 01, 02),
            102,
            new DateTimeImmutable('2015-01-03'),
            new DateTimeImmutable('2015-01-04')
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
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
            'mediumSynopsis' => 'Mediumest Synopsis',
            'longSynopsis' => 'Longest Synopsis',
            'image' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'streamable' => true,
            'mediaType' => MediaTypeEnum::UNKNOWN,
            'parent' => null,
            'firstBroadcastDate' => new DateTime(),
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 101,
            'duration' => 102,
            'streamableFrom' => new DateTime('2015-01-03'),
            'streamableUntil' => new DateTime('2015-01-04'),
        ];

        $expectedEntity = new Clip(
            1,
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $this->mockDefaultImage,
            1,
            2,
            true,
            true,
            MediaTypeEnum::UNKNOWN,
            null,
            101,
            null,
            [],
            [],
            new DateTimeImmutable(),
            new PartialDate(2015, 01, 02),
            102,
            new DateTimeImmutable('2015-01-03'),
            new DateTimeImmutable('2015-01-04')
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelSeriesWithParentProgramme()
    {
        $dbEntityArray = [
            'type' => 'series',
            'id' => '2',
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
            'aggregatedBroadcastsCount' => 11,
            'aggregatedEpisodesCount' => 12,
            'availableClipsCount' => 13,
            'availableEpisodesCount' => 14,
            'availableGalleriesCount' => 15,
            'isPodcastable' => false,
            'position' => 101,
            'firstBroadcastDate' => new DateTime(),
            'expectedChildCount' => 1001,
            'parent' => [
                'type' => 'brand',
                'id' => '1',
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
                'aggregatedBroadcastsCount' => 11,
                'aggregatedEpisodesCount' => 12,
                'availableClipsCount' => 13,
                'availableEpisodesCount' => 14,
                'availableGalleriesCount' => 15,
                'isPodcastable' => false,
                'parent' => null,
                'position' => 101,
                'firstBroadcastDate' => new DateTime(),
                'expectedChildCount' => 1001,
            ],
        ];

        $expectedEntity = new Series(
            2,
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $this->mockDefaultImage,
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
            new Brand(
                1,
                new Pid('b010t19z'),
                'Title',
                'Search Title',
                new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
                $this->mockDefaultImage,
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
                [],
                new DateTimeImmutable(),
                1001
            ),
            101,
            null,
            [],
            [],
            new DateTimeImmutable(),
            1001
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Could not find build domain model for unknown programme type "ham"
     */
    public function testUnknownProgrammeType()
    {
        $this->getMapper()->getDomainModel(['type' => 'ham']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Could not find build domain model for unknown programme type ""
     */
    public function testEmptyProgrammeType()
    {
        $this->getMapper()->getDomainModel([]);
    }
}
