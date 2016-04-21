<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTimeImmutable;
use PHPUnit_Framework_TestCase;

class NetworkTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $nid = new Nid('bbc_1xtra');
        $image = new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $network = new Network(
            $nid,
            'Name',
            $image
        );

        $this->assertEquals($nid, $network->getNid());
        $this->assertEquals('Name', $network->getName());
        $this->assertEquals($image, $network->getImage());

    }

    public function testConstructorOptionalArgs()
    {
        $nid = new Nid('bbc_1xtra');
        $image = new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $service = new Service(new Sid('bbc_1xtra'), '1xtra');

        $network = new Network(
            $nid,
            'Name',
            $image,
            'url_key',
            'Local Radio',
            NetworkMediumEnum::RADIO,
            $service,
            true,
            true,
            true,
            true,
            true
        );

        $this->assertEquals('url_key', $network->getUrlKey());
        $this->assertEquals('Local Radio', $network->getType());
        $this->assertEquals(NetworkMediumEnum::RADIO, $network->getMedium());
        $this->assertEquals($service, $network->getDefaultService());
        $this->assertEquals(true, $network->isPublicOutlet());
        $this->assertEquals(true, $network->isChildrens());
        $this->assertEquals(true, $network->isWorldServiceInternational());
        $this->assertEquals(true, $network->isInternational());
        $this->assertEquals(true, $network->isAllowedAdverts());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidMedium()
    {
        $nid = new Nid('bbc_1xtra');
        $image = new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');

        $network = new Network(
            $nid,
            'Name',
            $image,
            'url_key',
            'Local Radio',
            'wrongwrongwrong'
        );
    }
}
