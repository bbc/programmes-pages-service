<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use PHPUnit_Framework_TestCase;
use InvalidArgumentException;

class ContributionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('b0000001');
        $contributor = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Contributor');
        $segment = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Segment');

        $contribution = new Contribution(
            $pid,
            $contributor,
            $segment,
            'CreditRole'
        );

        $this->assertSame($pid, $contribution->getPid());
        $this->assertSame($contributor, $contribution->getContributor());
        $this->assertSame('CreditRole', $contribution->getCreditRole());
        $this->assertNull($contribution->getPosition());
        $this->assertNull($contribution->getCharacterName());
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
     * @expectedException InvalidArgumentException
     */
    public function testConstructorInvalidContributedTo()
    {
        $pid = new Pid('b0000001');
        $contributor = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Contributor');

        // ContributedTo cannot of type array
        $contributedTo = ['pid' => 's0000001'];

        $contribution = new Contribution(
            $pid,
            $contributor,
            $contributedTo,
            'CreditRole'
        );
    }

    /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     */
    public function testGetContributedToUnfetched()
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
}
