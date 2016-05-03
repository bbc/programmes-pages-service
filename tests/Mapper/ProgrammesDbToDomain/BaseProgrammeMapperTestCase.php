<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Enumeration\IsPodcastableEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

abstract class BaseProgrammeMapperTestCase extends BaseMapperTestCase
{
    protected $mockImageMapper;

    protected $mockMasterBrandMapper;

    protected $mockCategoryMapper;

    protected $mockRelatedLinkMapper;

    protected $mockDefaultImage;

    public function setUp()
    {
        $this->mockImageMapper = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ImageMapper'
        );

        $this->mockMasterBrandMapper = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MasterBrandMapper'
        );

        $this->mockCategoryMapper = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\CategoryMapper'
        );

        $this->mockRelatedLinkMapper = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\RelatedLinkMapper'
        );

        $this->mockDefaultImage = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Image'
        );

        $this->mockImageMapper->expects($this->any())
            ->method('getDefaultImage')
            ->willReturn($this->mockDefaultImage);
    }

    protected function getMapper(): ProgrammeMapper
    {
        return new ProgrammeMapper($this->getMapperProvider([
            'ImageMapper' => $this->mockImageMapper,
            'MasterBrandMapper' => $this->mockMasterBrandMapper,
            'CategoryMapper' => $this->mockCategoryMapper,
            'RelatedLinkMapper' => $this->mockRelatedLinkMapper,
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
        array $relatedLinks = []
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
            'isPodcastable' => IsPodcastableEnum::NO,
            'parent' => null,
            'releaseDate' => new PartialDate(2015, 01, 02),
            'position' => 101,
            'masterBrand' => $masterBrand,
            'categories' => $categories,
            'relatedLinks' => $relatedLinks,
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
        array $relatedLinks = []
    ) {
        return new Series(
            new Pid($pid),
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
            IsPodcastableEnum::NO,
            null,
            new PartialDate(2015, 01, 02),
            101,
            $masterBrand,
            $genres,
            $formats,
            $relatedLinks,
            1001
        );
    }
}
