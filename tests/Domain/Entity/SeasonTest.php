<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\Entity\Season;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedOptions;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use PHPUnit\Framework\TestCase;

class SeasonTest extends TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('zzzzzzzz');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $options = new Options(['two' => 2]);

        $season = new Season(
            [0, 1, 2],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            1103,
            1201,
            $options
        );

        $this->assertEquals(2, $season->getDbId());
        $this->assertEquals([0, 1, 2], $season->getDbAncestryIds());
        $this->assertEquals($pid, $season->getPid());
        $this->assertEquals('Title', $season->getTitle());
        $this->assertEquals('Search Title', $season->getSearchTitle());
        $this->assertEquals($synopses, $season->getSynopses());
        $this->assertEquals('Short Synopsis', $season->getShortSynopsis());
        $this->assertEquals('Longest Synopsis', $season->getLongestSynopsis());
        $this->assertEquals($image, $season->getImage());
        $this->assertEquals(1101, $season->getPromotionsCount());
        $this->assertEquals(1102, $season->getRelatedLinksCount());
        $this->assertEquals(1103, $season->getContributionsCount());
        $this->assertEquals(1201, $season->getAggregatedBroadcastsCount());
        $this->assertEquals($options, $season->getOptions());
        $this->assertSame(2, $season->getOption('two'));
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $parent = $this->createMock(Series::class);
        $masterBrand = $this->createMock(MasterBrand::class);

        $season = new Season(
            [0],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            1103,
            1201,
            new Options(),
            $parent,
            $masterBrand
        );

        $this->assertEquals($parent, $season->getParent());
        $this->assertEquals($masterBrand, $season->getMasterBrand());
    }

    public function testUnfetchedParent()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $season = new Season(
            [0],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            1103,
            1201,
            new Options(),
            new UnfetchedProgramme()
        );

        $this->expectException(DataNotFetchedException::class);
        $season->getParent();
    }

    public function testRequestingUnfetchedOptionsThrowsException()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $season = new Season(
            [0],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            1103,
            1201,
            new UnfetchedOptions()
        );

        $this->expectException(DataNotFetchedException::class);
        $this->expectExceptionMessage('Could not get options of Group "p01m5mss" as the full hierarchy was not fetched');
        $season->getOptions();
    }
}
