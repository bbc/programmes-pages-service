<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion;
use PHPUnit_Framework_TestCase;

class UnfetchedVersionTest extends PHPUnit_Framework_TestCase
{
    public function testUnfetchedVersion()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\Version',
            new UnfetchedVersion()
        );
    }
}
