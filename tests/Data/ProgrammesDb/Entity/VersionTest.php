<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use PHPUnit_Framework_TestCase;

class VersionTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $entity = new Version('pid');

        $this->assertSame(null, $entity->getId());
        $this->assertSame('pid', $entity->getPid());
        $this->assertSame(null, $entity->getDuration());
        $this->assertSame(null, $entity->getProgrammeItem());
        $this->assertEquals(new ArrayCollection(), $entity->getVersionTypes());
        $this->assertSame(null, $entity->getGuidanceWarningCodes());
        $this->assertSame(false, $entity->getCompetitionWarning());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new Version('pid');

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        $versionTypes = new ArrayCollection([1]);

        return [
            ['Pid', 'a-string'],
            ['Duration', 1],
            ['ProgrammeItem', new Episode('pid', 'title')],
            ['VersionTypes', $versionTypes],
            ['GuidanceWarningCodes', 'warningCodes'],
            ['CompetitionWarning', true],
        ];
    }
}
