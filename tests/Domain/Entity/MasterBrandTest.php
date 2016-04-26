<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use PHPUnit_Framework_TestCase;

class MasterBrandTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $mid = new Mid('bbc_1xtra');
        $image = new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $masterBrand = new MasterBrand(
            $mid,
            'Name',
            $image
        );

        $this->assertEquals($mid, $masterBrand->getMid());
        $this->assertEquals('Name', $masterBrand->getName());
        $this->assertEquals($image, $masterBrand->getImage());

    }

    public function testConstructorOptionalArgs()
    {
        $mid = new Mid('bbc_1xtra');
        $image = new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $network = new Network(new Nid('bbc_1xtra'), '1 Xtra', $image);

        $episode = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Episode'
        );

        $version = new Version(new Pid('b00tf1z5'), $episode);

        $masterBrand = new MasterBrand(
            $mid,
            'Name',
            $image,
            $network,
            $version
        );

        $this->assertEquals($network, $masterBrand->getNetwork());
        $this->assertEquals($version, $masterBrand->getCompetitionWarning());
    }
}
