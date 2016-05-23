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
        $this->assertSame($episode, $contribution->getContributionTo());
        $this->assertSame($episode, $contribution->getContributionToProgramme());
        $this->assertSame(null, $contribution->getContributionToSegment());
        $this->assertSame(null, $contribution->getContributionToVersion());
        $this->assertSame(null, $contribution->getPosition());
        $this->assertSame(null, $contribution->getCharacterName());

        $contribution = new Contribution('pid', $contributor, $creditRole, $segment);
        $this->assertSame($segment, $contribution->getContributionTo());
        $this->assertSame(null, $contribution->getContributionToProgramme());
        $this->assertSame($segment, $contribution->getContributionToSegment());
        $this->assertSame(null, $contribution->getContributionToVersion());

        $contribution = new Contribution('pid', $contributor, $creditRole, $version);
        $this->assertSame($version, $contribution->getContributionTo());
        $this->assertSame(null, $contribution->getContributionToProgramme());
        $this->assertSame(null, $contribution->getContributionToSegment());
        $this->assertSame($version, $contribution->getContributionToVersion());
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
     * @dataProvider setContributionToDataProvider
     */
    public function testSetContributionTo($contributionTo, $expectedProgramme, $expectedSegment, $expectedVersion)
    {
        $contributor = $this->mockContributor();
        $creditRole = $this->mockCreditRole();
        $episode = $this->mockEpisode();

        $contribution = new Contribution('pid', $contributor, $creditRole, $episode);
        $contribution->setContributionTo($contributionTo);

        $this->assertSame($contributionTo, $contribution->getContributionTo());
        $this->assertSame($expectedProgramme, $contribution->getContributionToProgramme());
        $this->assertSame($expectedSegment, $contribution->getContributionToSegment());
        $this->assertSame($expectedVersion, $contribution->getContributionToVersion());
    }

    public function setContributionToDataProvider()
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
    public function testInvalidContributionToThrowsExceptionOnConstruct()
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
    public function testInvalidContributionToThrowsExceptionOnSet()
    {
        $contribution = new Contribution(
            'pid',
            $this->mockContributor(),
            $this->mockCreditRole(),
            $this->mockEpisode()
        );
        $contribution->setContributionTo('wrongwrongwrong');
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
