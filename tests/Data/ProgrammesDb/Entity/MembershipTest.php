<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Membership;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class MembershipTest extends PHPUnit_Framework_TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(Membership::CLASS);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
        ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $group = $this->mockGroup();
        $episode = $this->mockEpisode();
        $image = $this->mockImage();

        $contribution = new Membership('pid', $group, $episode);
        $this->assertSame(null, $contribution->getId());
        $this->assertSame('pid', $contribution->getPid());
        $this->assertSame($group, $contribution->getGroup());
        $this->assertSame($episode, $contribution->getMember());
        $this->assertSame($episode, $contribution->getMemberCoreEntity());
        $this->assertSame(null, $contribution->getMemberImage());
        $this->assertSame(null, $contribution->getPosition());

        $contribution = new Membership('pid', $group, $image);
        $this->assertSame($image, $contribution->getMember());
        $this->assertSame(null, $contribution->getMemberCoreEntity());
        $this->assertSame($image, $contribution->getMemberImage());
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSetters($name, $validValue)
    {
        $group = $this->mockGroup();
        $episode = $this->mockEpisode();

        $contribution = new Membership('pid', $group, $episode);

        $contribution->{'set' . $name}($validValue);
        $this->assertEquals($validValue, $contribution->{'get' . $name}());
    }

    public function setterDataProvider()
    {
        $group = $this->mockGroup();

        return [
            ['Pid', 'a-string'],
            ['Group', $group],
            ['Position', 2],
        ];
    }

    /**
     * @dataProvider setMemberDataProvider
     */
    public function testSetMember($member, $expectedCoreEntity, $expectedImage)
    {
        $group = $this->mockGroup();
        $episode = $this->mockEpisode();

        $contribution = new Membership('pid', $group, $episode);
        $contribution->setMember($member);

        $this->assertSame($member, $contribution->getMember());
        $this->assertSame($expectedCoreEntity, $contribution->getMemberCoreEntity());
        $this->assertSame($expectedImage, $contribution->getMemberImage());
    }

    public function setMemberDataProvider()
    {
        $episode = $this->mockEpisode();
        $image = $this->mockImage();

        return [
            [$episode, $episode, null],
            [$image, null, $image],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidMemberThrowsExceptionOnConstruct()
    {
        new Membership(
            'pid',
            $this->mockGroup(),
            'wrongwrongwrong'
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidMemberThrowsExceptionOnSet()
    {
        $contribution = new Membership(
            'pid',
            $this->mockGroup(),
            $this->mockEpisode()
        );
        $contribution->setMember('wrongwrongwrong');
    }

    private function mockGroup()
    {
        return $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Group'
        );
    }

    private function mockEpisode()
    {
        return $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode'
        );
    }

    private function mockImage()
    {
        return $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image'
        );
    }
}
