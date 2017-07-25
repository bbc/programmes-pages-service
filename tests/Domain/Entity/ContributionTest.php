<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedGroup;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ContributionTest extends TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('b0000001');
        $contributor = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Contributor');
        $segment = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Segment');
        $version = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Version');
        $programme = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Programme');
        $group = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Group');

        $contribution = new Contribution(
            $pid,
            $contributor,
            $segment,
            'CreditRole'
        );

        $contribution2 = new Contribution(
            $pid,
            $contributor,
            $version,
            'CreditRole'
        );

        $contribution3 = new Contribution(
            $pid,
            $contributor,
            $programme,
            'CreditRole'
        );

        $contribution4 = new Contribution(
            $pid,
            $contributor,
            $group,
            'CreditRole'
        );

        $this->assertSame($pid, $contribution->getPid());
        $this->assertSame($contributor, $contribution->getContributor());
        $this->assertSame('CreditRole', $contribution->getCreditRole());
        $this->assertNull($contribution->getPosition());
        $this->assertNull($contribution->getCharacterName());

        $this->assertSame($segment, $contribution->getContributedTo());
        $this->assertSame($version, $contribution2->getContributedTo());
        $this->assertSame($programme, $contribution3->getContributedTo());
        $this->assertSame($group, $contribution4->getContributedTo());
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('b0000001');
        $contributor = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Contributor');
        $segment = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Segment');

        $contribution = new Contribution(
            $pid,
            $contributor,
            $segment,
            'CreditRole',
            1,
            'CharacterName'
        );

        $this->assertSame(1, $contribution->getPosition());
        $this->assertSame('CharacterName', $contribution->getCharacterName());
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testGetContributedToUnfetchedSegment()
    {
        $pid = new Pid('b0000001');
        $contributor = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Contributor');
        $unfetchedSegment = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedSegment');

        $contribution = new Contribution(
            $pid,
            $contributor,
            $unfetchedSegment,
            'CreditRole'
        );

        // Get the contributedTo
        $segment = $contribution->getContributedTo();
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testGetContributedToUnfetchedVersion()
    {
        $pid = new Pid('b0000001');
        $contributor = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Contributor');
        $unfetchedVersion = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion');

        $contribution = new Contribution(
            $pid,
            $contributor,
            $unfetchedVersion,
            'CreditRole'
        );

        // Get the contributedTo
        $version = $contribution->getContributedTo();
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testGetContributedToUnfetchedCoreEntity()
    {
        $pid = new Pid('b0000001');
        $contributor = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Contributor');
        $unfetchedCoreEntity = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme');

        $contribution = new Contribution(
            $pid,
            $contributor,
            $unfetchedCoreEntity,
            'CreditRole'
        );

        // Get the contributedTo
        $coreEntity = $contribution->getContributedTo();
    }

    public function testGetContributedToUnfetchedGroup()
    {
        $pid = new Pid('b0000001');
        $contributor = $this->createMock(Contributor::class);
        $unfetchedGroup = $this->createMock(UnfetchedGroup::class);

        $contribution = new Contribution(
            $pid,
            $contributor,
            $unfetchedGroup,
            'CreditRole'
        );

        $this->expectException(DataNotFetchedException::class);
        $contribution->getContributedTo();
    }
}
