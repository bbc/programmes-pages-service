<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Franchise;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedOptions;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use PHPUnit\Framework\TestCase;

class FranchiseTest extends TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('zzzzzzzz');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $options = new Options(['two' => 2]);

        $franchise = new Franchise(
            [0, 1, 2],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            1103,
            $options,
            1201
        );

        $this->assertEquals(2, $franchise->getDbId());
        $this->assertEquals([0, 1, 2], $franchise->getDbAncestryIds());
        $this->assertEquals($pid, $franchise->getPid());
        $this->assertEquals('Title', $franchise->getTitle());
        $this->assertEquals('Search Title', $franchise->getSearchTitle());
        $this->assertEquals($synopses, $franchise->getSynopses());
        $this->assertEquals('Short Synopsis', $franchise->getShortSynopsis());
        $this->assertEquals('Longest Synopsis', $franchise->getLongestSynopsis());
        $this->assertEquals($image, $franchise->getImage());
        $this->assertEquals(1101, $franchise->getPromotionsCount());
        $this->assertEquals(1102, $franchise->getRelatedLinksCount());
        $this->assertEquals(1103, $franchise->getContributionsCount());
        $this->assertEquals(1201, $franchise->getAggregatedBroadcastsCount());
        $this->assertEquals($options, $franchise->getOptions());
        $this->assertSame(2, $franchise->getOption('two'));
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $masterBrand = $this->createMock(MasterBrand::class);

        $franchise = new Franchise(
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
            1201,
            $masterBrand
        );

        $this->assertEquals($masterBrand, $franchise->getMasterBrand());
    }

    public function testRequestingUnfetchedOptionsThrowsException()
    {
        $pid = new Pid('p01m5mss');
        $synopses = new Synopses('Short Synopsis', 'Longest Synopsis', '');
        $image = new Image($pid, 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $franchise = new Franchise(
            [0],
            $pid,
            'Title',
            'Search Title',
            $synopses,
            $image,
            1101,
            1102,
            1103,
            new UnfetchedOptions(),
            1201
        );

        $this->expectException(DataNotFetchedException::class);
        $this->expectExceptionMessage('Could not get options of Group "p01m5mss" as the full hierarchy was not fetched');
        $franchise->getOptions();
    }
}
