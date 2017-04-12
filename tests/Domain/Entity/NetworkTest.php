<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedImage;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedService;
use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
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

        $this->assertSame($nid, $network->getNid());
        $this->assertSame('Name', $network->getName());
        $this->assertSame($image, $network->getImage());
        $this->assertNull($network->getUrlKey());
        $this->assertNull($network->getType());
        $this->assertSame(NetworkMediumEnum::UNKNOWN, $network->getMedium());
        $this->assertNull($network->getDefaultService());
        $this->assertSame(false, $network->isPublicOutlet());
        $this->assertSame(false, $network->isChildrens());
        $this->assertSame(false, $network->isWorldServiceInternational());
        $this->assertSame(false, $network->isInternational());
        $this->assertSame(false, $network->isAllowedAdverts());
    }

    public function testConstructorOptionalArgs()
    {
        $nid = new Nid('bbc_1xtra');
        $image = new Image(new Pid('p01m5mss'), 'Title', 'ShortSynopsis', 'LongestSynopsis', 'standard', 'jpg');
        $service = new Service(0, new Sid('bbc_1xtra'), new Pid('b0000001'), '1xtra');

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

        $this->assertSame('url_key', $network->getUrlKey());
        $this->assertSame('Local Radio', $network->getType());
        $this->assertSame(NetworkMediumEnum::RADIO, $network->getMedium());
        $this->assertSame($service, $network->getDefaultService());
        $this->assertSame(true, $network->isPublicOutlet());
        $this->assertSame(true, $network->isChildrens());
        $this->assertSame(true, $network->isWorldServiceInternational());
        $this->assertSame(true, $network->isInternational());
        $this->assertSame(true, $network->isAllowedAdverts());
    }

    /**
     * @expectedException \InvalidArgumentException
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

    /**
     * @dataProvider unfetchedProvider
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testTryingToFetchUnfetchedException($getter)
    {
        $entity = new Network(
            new Nid('bbc_1xtra'),
            'Name',
            new UnfetchedImage(),
            'url_key',
            'Local Radio',
            NetworkMediumEnum::UNKNOWN,
            new UnfetchedService()
        );

        $entity->$getter();
    }

    public function unfetchedProvider()
    {
        return [
            ['getImage'],
            ['getDefaultService'],
        ];
    }
}
