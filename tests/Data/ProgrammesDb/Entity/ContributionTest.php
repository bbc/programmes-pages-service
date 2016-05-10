<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Contribution;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class ContributionTest extends PHPUnit_Framework_TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Contribution::CLASS);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $contributor = $this->mockContributor();
        $creditRole = $this->mockCreditRole();
        $episode = $this->mockEpisode();
        $segment = $this->mockSegment();
        $version = $this->mockVersion();

        $contribution = new Contribution('pid', $contributor, $creditRole, $episode);
        $this->assertSame(null, $contribution->getId());
        $this->assertSame('pid', $contribution->getPid());
        $this->assertSame($contributor, $contribution->getContributor());
        $this->assertSame($creditRole, $contribution->getCreditRole());
        $this->assertSame($episode, $contribution->getContributedTo());
        $this->assertSame($episode, $contribution->getProgramme());
        $this->assertSame(null, $contribution->getSegment());
        $this->assertSame(null, $contribution->getVersion());
        $this->assertSame(null, $contribution->getPosition());
        $this->assertSame(null, $contribution->getCharacterName());

        $contribution = new Contribution('pid', $contributor, $creditRole, $segment);
        $this->assertSame($segment, $contribution->getContributedTo());
        $this->assertSame(null, $contribution->getProgramme());
        $this->assertSame($segment, $contribution->getSegment());
        $this->assertSame(null, $contribution->getVersion());

        $contribution = new Contribution('pid', $contributor, $creditRole, $version);
        $this->assertSame($version, $contribution->getContributedTo());
        $this->assertSame(null, $contribution->getProgramme());
        $this->assertSame(null, $contribution->getSegment());
        $this->assertSame($version, $contribution->getVersion());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $contributor = $this->mockContributor();
        $creditRole = $this->mockCreditRole();
        $episode = $this->mockEpisode();

        $contribution = new Contribution('pid', $contributor, $creditRole, $episode);

        $contribution->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $contribution->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        $contributor = $this->mockContributor();
        $creditRole = $this->mockCreditRole();

        return [
            ['Pid', 'a-string'],
            ['Contributor', $contributor],
            ['CreditRole', $creditRole],
            ['Position', 2],
            ['CharacterName', 'a-string'],
        ];
    }

    /**
     * @dataProvider setContributedToDataProvider
     */
    public function testSetContributedTo($contributedTo, $expectedProgramme, $expectedSegment, $expectedVersion)
    {
        $contributor = $this->mockContributor();
        $creditRole = $this->mockCreditRole();
        $episode = $this->mockEpisode();

        $contribution = new Contribution('pid', $contributor, $creditRole, $episode);
        $contribution->setContributedTo($contributedTo);

        $this->assertSame($contributedTo, $contribution->getContributedTo());
        $this->assertSame($expectedProgramme, $contribution->getProgramme());
        $this->assertSame($expectedSegment, $contribution->getSegment());
        $this->assertSame($expectedVersion, $contribution->getVersion());
    }

    public function setContributedToDataProvider()
    {
        $episode = $this->mockEpisode();
        $segment = $this->mockSegment();
        $version = $this->mockVersion();

        return [
            [$episode, $episode, null, null],
            [$segment, null, $segment, null],
            [$version, null, null, $version],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidContributedToThrowsExceptionOnConstruct()
    {
        new Contribution(
            'pid',
            $this->mockContributor(),
            $this->mockCreditRole(),
            'wrongwrongwrong'
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidContributedToThrowsExceptionOnSet()
    {
        $contribution = new Contribution(
            'pid',
            $this->mockContributor(),
            $this->mockCreditRole(),
            $this->mockEpisode()
        );
        $contribution->setContributedTo('wrongwrongwrong');
    }

    private function mockContributor()
    {
        return $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Contributor'
        );
    }

    private function mockCreditRole()
    {
        return $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CreditRole'
        );
    }

    private function mockEpisode()
    {
        return $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode'
        );
    }

    private function mockSegment()
    {
        return $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Segment'
        );
    }

    private function mockVersion()
    {
        return $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Version'
        );
    }
}
