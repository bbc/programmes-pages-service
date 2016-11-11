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
            'aggregatedBroadcastsCount' => 11,
            'aggregatedEpisodesCount' => 12,
            'availableClipsCount' => 13,
            'availableEpisodesCount' => 14,
            'availableGalleriesCount' => 15,
            'isPodcastable' => false,
            'contributionCount' => 22,
            'parent' => null,
            'position' => 101,
            'firstBroadcastDate' => new \DateTime(),
            'expectedChildCount' => 1001,
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
            11,
            12,
            13,
            14,
            15,
            false,
            22,
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
            'aggregatedBroadcastsCount' => 11,
            'aggregatedEpisodesCount' => 12,
            'availableClipsCount' => 13,
            'availableEpisodesCount' => 14,
            'availableGalleriesCount' => 15,
            'isPodcastable' => false,
            'contributionCount' => 22,
            'parent' => null,
            'position' => 101,
            'firstBroadcastDate' => new DateTime(),
            'expectedChildCount' => 1001,
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
            11,
            12,
            13,
            14,
            15,
            false,
            22,
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
            'mediaType' => MediaTypeEnum::UNKNOWN,
            'segmentEventCount' => 11,
            'aggregatedBroadcastsCount' => 101,
            'availableClipsCount' => 102,
            'availableGalleriesCount' => 103,
            'parent' => null,
            'contributionCount' => 22,
            'firstBroadcastDate' => new DateTime(),
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 1001,
            'duration' => 1002,
            'streamableFrom' => new DateTime('2015-01-03'),
            'streamableUntil' => new DateTime('2015-01-04'),
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
            MediaTypeEnum::UNKNOWN,
            11,
            101,
            102,
            103,
            22,
            null,
            1001,
            null,
            [],
            [],
            new DateTimeImmutable(),
            new PartialDate(2015, 01, 02),
            1002,
            new DateTimeImmutable('2015-01-03'),
            new DateTimeImmutable('2015-01-04')
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelClip()
    {
        $dbEntityArray = [
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
            'mediaType' => MediaTypeEnum::UNKNOWN,
            'segmentEventCount' => 11,
            'contributionCount' => 22,
            'parent' => null,
            'firstBroadcastDate' => new DateTime(),
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 1001,
            'duration' => 1002,
            'streamableFrom' => new DateTime('2015-01-03'),
            'streamableUntil' => new DateTime('2015-01-04'),
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
            MediaTypeEnum::UNKNOWN,
            11,
            22,
            null,
            1001,
            null,
            [],
            [],
            new DateTimeImmutable(),
            new PartialDate(2015, 01, 02),
            1002,
            new DateTimeImmutable('2015-01-03'),
            new DateTimeImmutable('2015-01-04')
        );

        $this->assertEquals($expectedEntity, $this->getMapper()->getDomainModel($dbEntityArray));
    }

    public function testGetDomainModelSeriesWithParentProgramme()
    {
        $dbEntityArray = [
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
            'aggregatedBroadcastsCount' => 11,
            'aggregatedEpisodesCount' => 12,
            'availableClipsCount' => 13,
            'availableEpisodesCount' => 14,
            'availableGalleriesCount' => 15,
            'isPodcastable' => false,
            'contributionCount' => 22,
            'position' => 101,
            'firstBroadcastDate' => new DateTime(),
            'expectedChildCount' => 1001,
            'parent' => [
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
                'aggregatedBroadcastsCount' => 11,
                'aggregatedEpisodesCount' => 12,
                'availableClipsCount' => 13,
                'availableEpisodesCount' => 14,
                'availableGalleriesCount' => 15,
                'isPodcastable' => false,
                'contributionCount' => 22,
                'parent' => null,
                'position' => 101,
                'firstBroadcastDate' => new DateTime(),
                'expectedChildCount' => 1001,
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
            11,
            12,
            13,
            14,
            15,
            false,
            22,
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
                11,
                12,
                13,
                14,
                15,
                false,
                22,
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
