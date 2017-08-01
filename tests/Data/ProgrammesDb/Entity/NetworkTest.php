<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Network;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class NetworkTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Network::class);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\OptionsTrait',
            ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = new Network('nid', 'name');

        $this->assertSame(null, $entity->getId());
        $this->assertSame('nid', $entity->getNid());
        $this->assertSame('name', $entity->getName());
        $this->assertSame(null, $entity->getUrlKey());
        $this->assertSame(null, $entity->getType());
        $this->assertSame(NetworkMediumEnum::UNKNOWN, $entity->getMedium());
        $this->assertSame(null, $entity->getImage());
        $this->assertSame(null, $entity->getDefaultService());
        $this->assertSame(false, $entity->getIsPublicOutlet());
        $this->assertSame(false, $entity->getIsChildrens());
        $this->assertSame(false, $entity->getIsWorldServiceInternational());
        $this->assertSame(false, $entity->getIsInternational());
        $this->assertSame(false, $entity->getIsAllowedAdverts());
        $this->assertSame(null, $entity->getStartDate());
        $this->assertSame(null, $entity->getEndDate());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new Network('nid', 'name');

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Nid', 'newNid'],
            ['Name', 'newName'],
            ['UrlKey', 'urlKey'],
            ['Type', 'type'],
            ['Medium', NetworkMediumEnum::RADIO],
            ['Image', new Image('pid', 'title')],
            ['Position', 101],
            ['DefaultService', new Service('sid', 'pid', 'name', 'type', 'mediaType')],
            ['IsPublicOutlet', true],
            ['IsChildrens', true],
            ['IsWorldServiceInternational', true],
            ['IsInternational', true],
            ['IsAllowedAdverts', true],
            ['StartDate', new DateTime()],
            ['EndDate', new DateTime()],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUnknownMediumThrowsException()
    {
        $entity = new Network('nid', 'name');

        $entity->setMedium('garbage');
    }
}
