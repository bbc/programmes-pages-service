<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use PHPUnit\Framework\TestCase;

class ContributorTest extends TestCase
{
    public function testConstructorRequiredArgs()
    {
        $pid = new Pid('b0000001');

        $contributor = new Contributor(
            0,
            $pid,
            'Type',
            'Name'
        );

        $this->assertSame(0, $contributor->getDbId());
        $this->assertSame($pid, $contributor->getPid());
        $this->assertSame('Type', $contributor->getType());
        $this->assertSame('Name', $contributor->getName());
        $this->assertNull($contributor->getMusicBrainzId());
    }

    public function testConstructorOptionalArgs()
    {
        $pid = new Pid('b0000001');

        $contributor = new Contributor(
            0,
            $pid,
            'Type',
            'Name',
            'Sort Name',
            'GivenName',
            'FamilyName',
            'musicBrainzGuid'
        );

        $this->assertSame('Sort Name', $contributor->getSortName());
        $this->assertSame('GivenName', $contributor->getGivenName());
        $this->assertSame('FamilyName', $contributor->getFamilyName());
        $this->assertSame('musicBrainzGuid', $contributor->getMusicBrainzId());
    }
}
