<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;
use PHPUnit\Framework\TestCase;

class UnfetchedProgrammeTest extends TestCase
{
    public function testUnfetchedProgramme()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\Programme',
            new UnfetchedProgramme()
        );
    }
}
