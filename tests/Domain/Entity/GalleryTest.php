<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Gallery;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedOptions;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use PHPUnit\Framework\TestCase;

class GalleryTest extends TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('zzzzzzzz');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $options = new Options(['two' => 2]);

        $gallery = new Gallery(
            [0, 1, 2],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            1103,
            $options
        );

        $this->assertEquals(2, $gallery->getDbId());
        $this->assertEquals([0, 1, 2], $gallery->getDbAncestryIds());
        $this->assertEquals($pid, $gallery->getPid());
        $this->assertEquals('Title', $gallery->getTitle());
        $this->assertEquals('Search Title', $gallery->getSearchTitle());
        $this->assertEquals($synopses, $gallery->getSynopses());
        $this->assertEquals('Short Synopsis', $gallery->getShortSynopsis());
        $this->assertEquals('Longest Synopsis', $gallery->getLongestSynopsis());
        $this->assertEquals($image, $gallery->getImage());
        $this->assertEquals(1101, $gallery->getPromotionsCount());
        $this->assertEquals(1102, $gallery->getRelatedLinksCount());
        $this->assertEquals(1103, $gallery->getContributionsCount());
        $this->assertEquals($options, $gallery->getOptions());
        $this->assertSame(2, $gallery->getOption('two'));
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $parent = $this->createMock(Series::class);
        $masterBrand = $this->createMock(MasterBrand::class);

        $gallery = new Gallery(
            [0],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            1103,
            new Options(),
            $parent,
            $masterBrand
        );

        $this->assertEquals($parent, $gallery->getParent());
        $this->assertEquals($masterBrand, $gallery->getMasterBrand());
    }

    public function testUnfetchedParent()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $gallery = new Gallery(
            [0],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            1103,
            new Options(),
            new UnfetchedProgramme()
        );

        $this->expectException(DataNotFetchedException::class);
        $gallery->getParent();
    }

    public function testRequestingUnfetchedOptionsThrowsException()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $gallery = new Gallery(
            [0],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            1103,
            new UnfetchedOptions()
        );

        $this->expectException(DataNotFetchedException::class);
        $this->expectExceptionMessage('Could not get options of Group "p01m5mss" as the full hierarchy was not fetched');
        $gallery->getOptions();
    }
}
