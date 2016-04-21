<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\VersionType;
use PHPUnit_Framework_TestCase;

class VersionTypeTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $versionType = new VersionType('Type', 'Name');

        $this->assertEquals('Type', $versionType->getType());
        $this->assertEquals('Name', $versionType->getName());
    }
}
