<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\VersionType;
use PHPUnit_Framework_TestCase;

class VersionTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = new Version();

        $this->assertEquals(null, $entity->getId());
        $this->assertEquals(null, $entity->getPid());
        $this->assertEquals(null, $entity->getDuration());
        $this->assertEquals(null, $entity->getProgrammeItem());
        $this->assertEquals(new ArrayCollection(), $entity->getVersionTypes());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new Version();

        $entity->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        $versionTypes = new ArrayCollection([1]);

        return [
            ['Pid', 'a-string'],
            ['Duration', '1'],
            ['ProgrammeItem', new Episode()],
            ['VersionTypes', $versionTypes],
        ];
    }

    public function testAddVersionType()
    {
        $vt = new VersionType();

        $version = new Version();
        $version->addVersionType($vt);

        $this->assertEquals(new ArrayCollection([$vt]), $version->getVersionTypes());
    }
}
