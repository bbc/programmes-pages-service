<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Network;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class MasterBrandTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(MasterBrand::CLASS);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $entity = new MasterBrand('mid', 'pid', 'name');

        $this->assertSame(null, $entity->getId());
        $this->assertSame('mid', $entity->getMid());
        $this->assertSame('pid', $entity->getPid());
        $this->assertSame('name', $entity->getName());
        $this->assertSame(null, $entity->getNetwork());
        $this->assertSame(null, $entity->getImage());
        $this->assertSame(null, $entity->getCompetitionWarning());
        $this->assertSame(null, $entity->getColour());
        $this->assertSame(null, $entity->getUrlKey());
        $this->assertSame(null, $entity->getPosition());
        $this->assertSame(null, $entity->getStartDate());
        $this->assertSame(null, $entity->getEndDate());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new MasterBrand('mid', 'pid', 'name');

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Mid', 'newMid'],
            ['Name', 'newName'],
            ['Network', new Network('nid', 'network')],
            ['Image', new Image('pid', 'title')],
            ['CompetitionWarning', new Version('vpid', new Episode('p0000001', 'Ep'))],
            ['Colour', 'colour'],
            ['UrlKey', 'urlkey'],
            ['Position', 1],
            ['StartDate', new DateTime()],
            ['EndDate', new DateTime()],
        ];
    }
}
