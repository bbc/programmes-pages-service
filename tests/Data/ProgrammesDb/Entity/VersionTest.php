<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class VersionTest extends PHPUnit_Framework_TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Version::CLASS);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $episode = new Episode('pid', 'title');
        $entity = new Version('pid', $episode);

        $this->assertSame(null, $entity->getId());
        $this->assertSame('pid', $entity->getPid());
        $this->assertSame(null, $entity->getDuration());
        $this->assertSame($episode, $entity->getProgrammeItem());
        $this->assertEquals(new ArrayCollection(), $entity->getVersionTypes());
        $this->assertSame(null, $entity->getGuidanceWarningCodes());
        $this->assertSame(false, $entity->getCompetitionWarning());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $entity = new Version('pid', new Episode('pid', 'title'));

        $entity->{'set' . $name}($validValue);
        $this->assertSame($validValue, $entity->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        $versionTypes = new ArrayCollection([1]);

        return [
            ['Pid', 'a-string'],
            ['Duration', 1],
            ['VersionTypes', $versionTypes],
            ['GuidanceWarningCodes', 'warningCodes'],
            ['CompetitionWarning', true],
        ];
    }
}
