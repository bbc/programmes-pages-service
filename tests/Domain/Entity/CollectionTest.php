<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Collection;
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

class CollectionTest extends TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('zzzzzzzz');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $options = new Options(['two' => 2]);

        $collection = new Collection(
            [0, 1, 2],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            1103,
            false,
            $options
        );

        $this->assertEquals(2, $collection->getDbId());
        $this->assertEquals([0, 1, 2], $collection->getDbAncestryIds());
        $this->assertEquals($pid, $collection->getPid());
        $this->assertEquals('Title', $collection->getTitle());
        $this->assertEquals('Search Title', $collection->getSearchTitle());
        $this->assertEquals($synopses, $collection->getSynopses());
        $this->assertEquals('Short Synopsis', $collection->getShortSynopsis());
        $this->assertEquals('Longest Synopsis', $collection->getLongestSynopsis());
        $this->assertEquals($image, $collection->getImage());
        $this->assertEquals(1101, $collection->getPromotionsCount());
        $this->assertEquals(1102, $collection->getRelatedLinksCount());
        $this->assertEquals(1103, $collection->getContributionsCount());
        $this->assertEquals(false, $collection->isPodcastable());
        $this->assertEquals($options, $collection->getOptions());
        $this->assertSame(2, $collection->getOption('two'));
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $parent = $this->createMock(Series::class);
        $masterBrand = $this->createMock(MasterBrand::class);

        $collection = new Collection(
            [0],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            1103,
            true,
            new Options(),
            $parent,
            $masterBrand
        );

        $this->assertEquals($parent, $collection->getParent());
        $this->assertEquals($masterBrand, $collection->getMasterBrand());
    }

    public function testUnfetchedParent()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $collection = new Collection(
            [0],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            1103,
            true,
            new Options(),
            new UnfetchedProgramme()
        );

        $this->expectException(DataNotFetchedException::class);
        $collection->getParent();
    }

    public function testRequestingUnfetchedOptionsThrowsException()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $collection = new Collection(
            [0],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            1103,
            true,
            new UnfetchedOptions()
        );

        $this->expectException(DataNotFetchedException::class);
        $this->expectExceptionMessage('Could not get options of Group "p01m5mss" as the full hierarchy was not fetched');
        $collection->getOptions();
    }
}
