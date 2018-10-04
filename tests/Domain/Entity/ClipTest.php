<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Clip;
use BBC\ProgrammesPagesService\Domain\Entity\Format;
use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedOptions;
use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ClipTest extends TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $options = new Options(['two' => 2]);

        $programme = new Clip(
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
            MediaTypeEnum::UNKNOWN,
            1201,
            1104,
            true,
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
        $this->assertEquals(true, $programme->isStreamableAlternate());
        $this->assertEquals(1103, $programme->getContributionsCount());
        $this->assertEquals(MediaTypeEnum::UNKNOWN, $programme->getMediaType());
        $this->assertEquals(1201, $programme->getSegmentEventCount());
        $this->assertEquals(1104, $programme->getAggregatedGalleriesCount());
        $this->assertEquals(true, $programme->isExternallyEmbeddable());
        $this->assertEquals($options, $programme->getOptions());
        $this->assertSame(2, $programme->getOption('two'));
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $parent = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Series');
        $masterBrand = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\MasterBrand');
        $releaseDate = new PartialDate(2015, 01, 02);

        $genre = new Genre([0], 'id', 'Title', 'url_key');
        $format = new Format([1], 'id2', 'Title', 'url_key');

        $streamableFrom = new DateTimeImmutable();
        $streamableUntil = new DateTimeImmutable();

        $firstBroadcastDate = new \DateTimeImmutable();

        $programme = new Clip(
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
            MediaTypeEnum::UNKNOWN,
            1201,
            1104,
            false,
            new Options(),
            $parent,
            2101,
            $masterBrand,
            [$genre],
            [$format],
            $firstBroadcastDate,
            $releaseDate,
            2201,
            $streamableFrom,
            $streamableUntil,
            ['media_set_1', 'media_set_2']
        );

        $this->assertEquals($parent, $programme->getParent());
        $this->assertEquals($releaseDate, $programme->getReleaseDate());
        $this->assertEquals(2101, $programme->getPosition());
        $this->assertEquals($masterBrand, $programme->getMasterBrand());
        $this->assertEquals([$genre], $programme->getGenres());
        $this->assertEquals([$format], $programme->getFormats());
        $this->assertEquals($firstBroadcastDate, $programme->getFirstBroadcastDate());
        $this->assertEquals(2201, $programme->getDuration());
        $this->assertEquals($streamableFrom, $programme->getStreamableFrom());
        $this->assertEquals($streamableUntil, $programme->getStreamableUntil());
        $this->assertEquals(true, $programme->isDownloadable());
    }

    public function testHasFutureAvailability()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $parent = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Series');
        $masterBrand = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\MasterBrand');
        $releaseDate = new PartialDate(2015, 01, 02);

        $genre = new Genre([0], 'id', 'Title', 'url_key');
        $format = new Format([1], 'id2', 'Title', 'url_key');

        $streamableFrom = new DateTimeImmutable();
        $streamableUntil = new DateTimeImmutable();

        $firstBroadcastDate = new \DateTimeImmutable();

        $programme = new Clip(
            [0],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            true,
            false,
            true,
            1103,
            MediaTypeEnum::UNKNOWN,
            1201,
            1104,
            true,
            new Options(),
            $parent,
            2101,
            $masterBrand,
            [$genre],
            [$format],
            $firstBroadcastDate,
            $releaseDate,
            2201,
            $streamableFrom,
            $streamableUntil
        );

        $this->assertEquals(true, $programme->hasFutureAvailability());
    }

    public function testDoesntHaveFutureAvailability()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $parent = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Series');
        $masterBrand = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\MasterBrand');
        $releaseDate = new PartialDate(2015, 01, 02);

        $genre = new Genre([0], 'id', 'Title', 'url_key');
        $format = new Format([1], 'id2', 'Title', 'url_key');

        $firstBroadcastDate = new \DateTimeImmutable();

        $programme = new Clip(
            [0],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            true,
            false,
            true,
            1103,
            MediaTypeEnum::UNKNOWN,
            1201,
            1104,
            false,
            new Options(),
            $parent,
            2101,
            $masterBrand,
            [$genre],
            [$format],
            $firstBroadcastDate,
            $releaseDate,
            2201
        );

        $this->assertEquals(false, $programme->hasFutureAvailability());
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get options of CoreEntity "p01m5mss" as the full hierarchy was not fetched
     */
    public function testRequestingUnfetchedOptionsThrowsException()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $programme = new Clip(
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
            'audio',
            1201,
            1104,
            true,
            new UnfetchedOptions()
        );

        $programme->getOptions();
    }

    /**
     * @dataProvider playableCasesDataProvider
     */
    public function testPlayableCases($mediaType, $masterbrand, $streamable, $expectedPlayable)
    {

        $programme = new Clip(
            [0],
            new Pid('p01m5mss'),
            'Title',
            'Search Title',
            new Synopses('Short Synopsis', 'Longest Synopsis', ''),
            new Image(new Pid('p0000001'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg'),
            0,
            0,
            true,
            $streamable,
            false,
            3,
            $mediaType,
            0,
            0,
            false,
            new UnfetchedOptions(),
            null,
            null,
            $masterbrand
        );

        $this->assertEquals($expectedPlayable, $programme->isPlayable());
    }

    public function playableCasesDataProvider()
    {
        return [
            [MediaTypeEnum::AUDIO, $this->mockMasterBrand(true), true, true],
            [MediaTypeEnum::VIDEO, $this->mockMasterBrand(false), true, true],
            [MediaTypeEnum::AUDIO, $this->mockMasterBrand(false), true, true],
            [MediaTypeEnum::UNKNOWN, $this->mockMasterBrand(false), true, true],
            [MediaTypeEnum::AUDIO, $this->mockMasterBrand(true), false, false],
            [MediaTypeEnum::VIDEO, $this->mockMasterBrand(true), false, false],
        ];
    }

    private function mockMasterBrand($isStreamableInPlayspace)
    {
        return $this->createConfiguredMock(MasterBrand::class, [
            'isStreamableInPlayspace' => $isStreamableInPlayspace,
        ]);
    }
}
