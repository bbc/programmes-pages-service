<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Brand;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
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
            'alternativeImage' => null,
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
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 101,
            'expectedChildCount' => 1001,
        ];

        $expectedEntity = new Brand(
            1,
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            'Short Synopsis',
            'Longest Synopsis',
            $this->mockDefaultImage,
            null,
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
            new PartialDate(2015, 01, 02),
            101,
            null,
            [],
            [],
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
            'alternativeImage' => null,
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
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 101,
            'expectedChildCount' => 1001,
        ];

        $expectedEntity = new Series(
            1,
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            'Short Synopsis',
            'Longest Synopsis',
            $this->mockDefaultImage,
            null,
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
            new PartialDate(2015, 01, 02),
            101,
            null,
            [],
            [],
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
            'alternativeImage' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'streamable' => true,
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
            1,
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            'Short Synopsis',
            'Longest Synopsis',
            $this->mockDefaultImage,
            null,
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
            null,
            [],
            [],
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
            'longSynopsis' => 'Longest Synopsis',
            'image' => null,
            'alternativeImage' => null,
            'promotionsCount' => 1,
            'relatedLinksCount' => 2,
            'hasSupportingContent' => true,
            'streamable' => true,
            'mediaType' => MediaTypeEnum::UNKNOWN,
            'parent' => null,
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
            'Short Synopsis',
            'Longest Synopsis',
            $this->mockDefaultImage,
            null,
            1,
            2,
            true,
            true,
            MediaTypeEnum::UNKNOWN,
            null,
            new PartialDate(2015, 01, 02),
            101,
            null,
            [],
            [],
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
            'longSynopsis' => 'Longest Synopsis',
            'image' => null,
            'alternativeImage' => null,
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
                'longSynopsis' => 'Longest Synopsis',
                'image' => null,
                'alternativeImage' => null,
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
                'releaseDate' => new PartialDate(2015, 01, 02),
                'position' => 101,
                'expectedChildCount' => 1001,
            ],
        ];

        $expectedEntity = new Series(
            2,
            new Pid('b010t19z'),
            'Title',
            'Search Title',
            'Short Synopsis',
            'Longest Synopsis',
            $this->mockDefaultImage,
            null,
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
                'Short Synopsis',
                'Longest Synopsis',
                $this->mockDefaultImage,
                null,
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
                new PartialDate(2015, 01, 02),
                101,
                null,
                [],
                [],
                1001
            ),
            new PartialDate(2015, 01, 02),
            101,
            null,
            [],
            [],
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
