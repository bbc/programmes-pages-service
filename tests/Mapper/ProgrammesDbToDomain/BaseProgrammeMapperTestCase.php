<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

abstract class BaseProgrammeMapperTestCase extends BaseMapperTestCase
{
    protected $mockImageMapper;

    protected $mockMasterBrandMapper;

    protected $mockOptionsMapper;

    protected $mockCategoryMapper;

    protected $mockDefaultImage;

    protected $mockOptions;

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

        $this->mockOptionsMapper = $this->createMock(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\OptionsMapper'
        );

        $this->mockDefaultImage = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Image'
        );

        $this->mockOptions = $this->createMock(
            'BBC\ProgrammesPagesService\Domain\Entity\Options'
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

    /*
     * A sample DB Entity that can be used for testing any mappers that the
     * ProgrammeMapper depends upon.
     */
    protected function getSampleProgrammeDbEntity(
        string $pid,
        array $image = null,
        array $masterBrand = null,
        array $categories = [],
        array $parent = null,
        int $id = 1,
        array $options = null
    ) {
        return [
            'id' => $id,
            'type' => 'series',
            'ancestry' => $id . ',',
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
            'streamableAlternate' => true,
            'contributionsCount' => 10,
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
            'firstBroadcastDate' => new \DateTime('2017-01-03T18:00:00Z'),
            'expectedChildCount' => 1001,
            'options' => $options,
        ];
    }

    /*
     * A sample expected domain model that can be used for testing any mappers
     * that the ProgrammeMapper depends upon.
     */
    protected function getSampleProgrammeDomainEntity(
        string $pid,
        Image $image = null,
        MasterBrand $masterBrand = null,
        array $genres = [],
        array $formats = [],
        Programme $parent = null,
        int $id = 1,
        ?Options $options = null
    ) {
        return new Series(
            [$id],
            new Pid($pid),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Mediumest Synopsis', 'Longest Synopsis'),
            $image,
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
            $parent,
            101,
            $masterBrand,
            $genres,
            $formats,
            new \DateTimeImmutable('2017-01-03T18:00:00Z'),
            1001,
            $options
        );
    }
}
