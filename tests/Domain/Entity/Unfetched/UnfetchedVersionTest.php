<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion;
use PHPUnit\Framework\TestCase;

class UnfetchedVersionTest extends TestCase
{
    public function testUnfetchedVersion()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\Version',
            new UnfetchedVersion()
        );
    }
}
