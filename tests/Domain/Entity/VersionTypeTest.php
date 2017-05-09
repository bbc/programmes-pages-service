<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\VersionType;
use PHPUnit\Framework\TestCase;

class VersionTypeTest extends TestCase
{
    public function testConstructorRequiredArgs()
    {
        $versionType = new VersionType('Type', 'Name');

        $this->assertEquals('Type', $versionType->getType());
        $this->assertEquals('Name', $versionType->getName());
    }
}
