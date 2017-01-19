<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedNetwork;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion;
use PHPUnit_Framework_TestCase;

class MasterBrandTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $mid = new Mid('bbc_1xtra');
        $image = new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $network = new Network(new Nid('bbc_1xtra'), '1 Xtra', $image);
        $options = new Options(['one' => 1]);

        $masterBrand = new MasterBrand(
            $mid,
            'Name',
            $image,
            $network,
            $options
        );

        $this->assertEquals($mid, $masterBrand->getMid());
        $this->assertEquals('Name', $masterBrand->getName());
        $this->assertEquals($image, $masterBrand->getImage());
        $this->assertEquals($network, $masterBrand->getNetwork());
        $this->assertEquals($options, $masterBrand->getOptions());
        $this->assertSame(1, $masterBrand->getOption('one'));
    }

    public function testConstructorOptionalArgs()
    {
        $mid = new Mid('bbc_1xtra');
        $image = new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $network = new Network(new Nid('bbc_1xtra'), '1 Xtra', $image);
        $options = new Options(['one' => 1]);

        $version = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Version');

        $masterBrand = new MasterBrand(
            $mid,
            'Name',
            $image,
            $network,
            $options,
            $version
        );

        $this->assertEquals($version, $masterBrand->getCompetitionWarning());
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get Competition Warning of MasterBrand "bbc_1xtra" as it was not fetched
     */
    public function testRequestingUnfetchedCompetitionWarningThrowsException()
    {
        $pid = new Pid('p01m5mss');
        $episode = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Episode');

        $mid = new Mid('bbc_1xtra');
        $image = new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $network = new Network(new Nid('bbc_1xtra'), '1 Xtra', $image);

        $masterBrand = new MasterBrand(
            $mid,
            'Name',
            $image,
            $network,
            new Options([]),
            new UnfetchedVersion()
        );

        $masterBrand->getCompetitionWarning();
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get Network of MasterBrand "bbc_1xtra" as it was not fetched
     */
    public function testRequestingUnfetchedNetworkThrowsException()
    {
        $pid = new Pid('p01m5mss');
        $episode = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Episode');

        $mid = new Mid('bbc_1xtra');
        $image = new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $network = new UnfetchedNetwork();
        $options = new Options(['one' => 1]);

        $masterBrand = new MasterBrand(
            $mid,
            'Name',
            $image,
            $network,
            $options
        );

        $masterBrand->getNetwork();
    }
}
