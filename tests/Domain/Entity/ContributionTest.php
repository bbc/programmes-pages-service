<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use PHPUnit_Framework_TestCase;

class ContributionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('b0000001');
        $contributor = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Contributor');

        $contribution = new Contribution(
            $pid,
            $contributor,
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

        $contribution = new Contribution(
            $pid,
            $contributor,
            'CreditRole',
            1,
            'CharacterName'
        );

        $this->assertSame(1, $contribution->getPosition());
        $this->assertSame('CharacterName', $contribution->getCharacterName());
    }
}
