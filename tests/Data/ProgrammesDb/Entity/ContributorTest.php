<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Contributor;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ContributorTest extends TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Contributor::CLASS);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\PartnerPidTrait',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $contributor = new Contributor('pid', 'type');
        $this->assertSame(null, $contributor->getId());
        $this->assertSame('pid', $contributor->getPid());
        $this->assertSame('type', $contributor->getType());
        $this->assertSame('', $contributor->getName());
        $this->assertSame(null, $contributor->getMusicBrainzId());
        $this->assertSame(null, $contributor->getPresentationName());
        $this->assertSame(null, $contributor->getGivenName());
        $this->assertSame(null, $contributor->getFamilyName());
        $this->assertSame(null, $contributor->getSortName());
        $this->assertSame(null, $contributor->getNameLanguage());
        $this->assertSame('', $contributor->getDisambiguation());
        $this->assertSame(null, $contributor->getGender());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $contributor = new Contributor('pid', 'type');

        $contributor->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $contributor->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        return [
            ['Pid', 'a-string'],
            ['Type', 'a-string'],
            ['Name', 'a-string'],
            ['MusicBrainzId', 'a-string'],
            ['PresentationName', 'a-string'],
            ['GivenName', 'a-string'],
            ['FamilyName', 'a-string'],
            ['SortName', 'a-string'],
            ['NameLanguage', 'a-string'],
            ['Disambiguation', 'a-string'],
            ['Gender', 'a-string'],
        ];
    }
}
