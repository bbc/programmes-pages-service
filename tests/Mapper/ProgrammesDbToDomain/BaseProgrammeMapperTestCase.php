<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

abstract class BaseProgrammeMapperTestCase extends BaseMapperTestCase
{
    protected $mockImageMapper;

    protected $mockMasterBrandMapper;

    protected $mockCategoryMapper;

    protected $mockDefaultImage;

    public function setUp()
    {
        $this->mockImageMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ImageMapper'
        );

        $this->mockMasterBrandMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MasterBrandMapper'
        );

        $this->mockCategoryMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CategoryMapper'
        );

        $this->mockDefaultImage = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Image'
        );

        $this->mockDefaultImage->method('getTitle')->willReturn('DefaultImage');

        $this->mockImageMapper->expects($this->any())
            ->method('getDefaultImage')
            ->willReturn($this->mockDefaultImage);

    }

    protected function getMapper(): ProgrammeMapper
    {
        return new ProgrammeMapper($this->getMapperFactory([
            'ImageMapper' => $this->mockImageMapper,
            'MasterBrandMapper' => $this->mockMasterBrandMapper,
            'CategoryMapper' => $this->mockCategoryMapper,
        ]));
    }

    /**
     * A sample DB Entity that can be used for testing any mappers that the
     * ProgrammeMapper depends upon.
     */
    protected function getSampleProgrammeDbEntity(
        $pid,
        $image = null,
        $masterBrand = null,
        array $categories = [],
        array $parent = null
    ) {
        return [
            'type' => 'series',
            'id' => '1',
            'pid' => $pid,
            'title' => 'Title',
            'searchTitle' => 'Search Title',
            'shortSynopsis' => 'Short Synopsis',
            'mediumSynopsis' => 'Mediumest Synopsis',
            'longSynopsis' => 'Longest Synopsis',
            'image' => $image,
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
            'parent' => $parent,
            'position' => 101,
            'masterBrand' => $masterBrand,
            'categories' => $categories,
            'firstBroadcastDate' => new \DateTime(),
            'expectedChildCount' => 1001,
        ];
    }

    /**
     * A sample expected domain model that can be used for testing any mappers
     * that the ProgrammeMapper depends upon.
     */
    protected function getSampleProgrammeDomainEntity(
        $pid,
        $image = null,
        $masterBrand = null,
        array $genres = [],
        array $formats = [],
        Programme $parent = null
    ) {
        return new Series(
            1,
            new Pid($pid),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
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
            $genres,
            $formats,
            new \DateTimeImmutable(),
            1001
        );
    }
}
