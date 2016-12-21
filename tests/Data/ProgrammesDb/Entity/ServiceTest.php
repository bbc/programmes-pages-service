<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Network;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Service;
use DateTime;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class ServiceTest extends PHPUnit_Framework_TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Service::CLASS);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = new Service('sid', 'pid', 'name', 'type', 'mediaType');

        $this->assertSame(null, $entity->getId());
        $this->assertSame('sid', $entity->getSid());
        $this->assertSame('pid', $entity->getPid());
        $this->assertSame('type', $entity->getType());
        $this->assertSame('name', $entity->getName());
        $this->assertSame('name', $entity->getShortName());
        $this->assertSame('sid', $entity->getUrlKey());
        $this->assertSame('mediaType', $entity->getMediaType());
        $this->assertSame(null, $entity->getMasterBrand());
        $this->assertSame(null, $entity->getNetwork());
        $this->assertSame(null, $entity->getStartDate());
        $this->assertSame(null, $entity->getEndDate());
        $this->assertSame(null, $entity->getLiveStreamUrl());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new Service('sid', 'pid', 'name', 'type', 'mediaType');

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Sid', 'newSid'],
            ['Type', 'newType'],
            ['Name', 'newName'],
            ['ShortName', 'newShortName'],
            ['UrlKey', 'newUrlKey'],
            ['MediaType', 'newMediaType'],
            ['MasterBrand', new MasterBrand('mid', 'pid', 'masterbrand')],
            ['Network', new Network('nid', 'network')],
            ['StartDate', new DateTime()],
            ['EndDate', new DateTime()],
            ['LiveStreamUrl', ''],
        ];
    }
}
